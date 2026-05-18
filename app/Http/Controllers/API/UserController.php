<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\PersistsAdminNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    use PersistsAdminNotifications;

    /** FQCN stored in personal_access_tokens.tokenable_type */
    private const USER_TYPE = 'App\\Models\\User';

    /** Canonical roles */
    private const ROLES = ['admin', 'hr', 'employee'];

    /** Short codes for roles */
    private const ROLE_SHORT = [
        'admin'    => 'ADM',
        'hr'       => 'HR',
        'employee' => 'EMP',
    ];

    /* =========================================================
     |                       AUTH
     |=========================================================*/

    /**
     * Reusable activity logger.
     */
    private function logActivity(
        string $activity,
        string $title,
        string $description,
        ?int $performedBy = null,
        ?string $performedByName = null,
        $targetId = null,
        ?string $targetType = 'user',
        array $properties = [],
        ?Request $request = null,
        string $module = 'users'
    ): void {
        try {
            $actorId  = $performedBy ?: (is_numeric($targetId) ? (int) $targetId : 0);
            $recordId = is_numeric($targetId) ? (int) $targetId : ($performedBy ?: null);

            $noteParts = array_filter([
                trim($title),
                trim($description),
                $performedByName ? ('Actor: ' . $performedByName) : null,
            ]);

            $logNote       = implode(' — ', $noteParts);
            $changedFields = !empty($properties) ? array_values(array_map('strval', array_keys($properties))) : null;
            $newValues     = !empty($properties) ? $properties : null;

            DB::table('user_data_activity_log')->insert([
                'performed_by'      => $actorId,
                'performed_by_role' => isset($properties['role']) && $properties['role'] !== null
                    ? (string) $properties['role']
                    : null,
                'ip'             => $request?->ip(),
                'user_agent'     => $request?->userAgent(),
                'activity'       => $activity,
                'module'         => $module,
                'table_name'     => $targetType === 'user' ? 'users' : (string) ($targetType ?: 'users'),
                'record_id'      => $recordId,
                'changed_fields' => $changedFields ? json_encode($changedFields, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'old_values'     => null,
                'new_values'     => $newValues ? json_encode($newValues, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'log_note'       => $logNote ?: null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Activity Log] failed', [
                'activity'     => $activity,
                'performed_by' => $performedBy,
                'target_id'    => $targetId,
                'error'        => $e->getMessage(),
            ]);
        }
    }

    /**
     * POST /api/auth/login
     * Body: { login, password, remember?: bool }
     * login can be email or phone number.
     */
    public function login(Request $request)
    {
        Log::info('[Auth Login] begin', ['ip' => $request->ip()]);

        $validated = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
            'remember' => 'sometimes|boolean',
        ]);

        $loginInput = trim((string) $validated['login']);
        $isEmail    = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

        $user = DB::table('users')
            ->when(
                $isEmail,
                fn($q) => $q->where('email', $loginInput),
                fn($q) => $q->where('phone_number', $loginInput)
            )
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            $this->logActivity(
                activity: 'login_failed',
                title: 'Login failed - user not found',
                description: 'Login attempt failed because no user was found for the provided email or phone number.',
                performedBy: 0,
                performedByName: null,
                targetId: null,
                targetType: 'user',
                properties: [
                    'login'  => $loginInput,
                    'reason' => 'user_not_found',
                ],
                request: $request
            );

            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (isset($user->status) && $user->status !== 'active') {
            $this->logActivity(
                activity: 'login_blocked',
                title: 'Login blocked - inactive account',
                description: 'Login attempt blocked because the account is not active.',
                performedBy: (int) $user->id,
                performedByName: $user->name ?? null,
                targetId: $user->id,
                targetType: 'user',
                properties: [
                    'login'  => $loginInput,
                    'status' => $user->status,
                    'reason' => 'inactive_account',
                    'role'   => $user->role ?? null,
                ],
                request: $request
            );

            return response()->json([
                'status'  => 'error',
                'message' => 'Account is not active',
            ], 403);
        }

        if (!Hash::check($validated['password'], $user->password)) {
            $this->logActivity(
                activity: 'login_failed',
                title: 'Login failed - password mismatch',
                description: 'Login attempt failed because the password did not match.',
                performedBy: (int) $user->id,
                performedByName: $user->name ?? null,
                targetId: $user->id,
                targetType: 'user',
                properties: [
                    'login'  => $loginInput,
                    'reason' => 'password_mismatch',
                    'role'   => $user->role ?? null,
                ],
                request: $request
            );

            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $remember  = (bool) ($validated['remember'] ?? false);
        $expiresAt = $remember ? now()->addDays(30) : now()->addHours(12);
        $plainToken = $this->issueToken((int) $user->id, $expiresAt);

        DB::table('users')->where('id', $user->id)->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'updated_at'    => now(),
        ]);

        $freshUser = DB::table('users')->where('id', $user->id)->first();

        $this->logActivity(
            activity: 'login',
            title: 'User login successful',
            description: 'User logged in successfully.',
            performedBy: (int) $user->id,
            performedByName: $user->name ?? null,
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'login'      => $loginInput,
                'remember'   => $remember,
                'expires_at' => $expiresAt->toIso8601String(),
                'role'       => $freshUser->role ?? null,
            ],
            request: $request
        );

        return response()->json([
            'status'       => 'success',
            'message'      => 'Login successful',
            'access_token' => $plainToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $expiresAt->toIso8601String(),
            'user'         => $this->publicUserPayload($freshUser),
        ]);
    }

    /**
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        return $this->registerEmployee($request);
    }

    protected function registerEmployee(Request $request)
    {
        Log::info('[Register] begin', ['ip' => $request->ip()]);

        $rules = [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'required|string|max:32',
            'source'       => 'nullable|string|max:100',
        ];

        if ($request->filled('password') || $request->filled('password_confirmation')) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['password_confirmation'] = 'required|string|min:8';
        } else {
            $rules['password'] = 'nullable|string|min:8';
        }

        $v = Validator::make($request->all(), $rules);

        if ($v->fails()) {
            $this->logActivity(
                activity: 'store_failed',
                title: 'Registration failed - validation error',
                description: 'Account registration failed due to validation errors.',
                performedBy: 0,
                performedByName: null,
                targetId: null,
                targetType: 'user',
                properties: [
                    'email'        => $request->input('email'),
                    'phone_number' => $request->input('phone_number'),
                    'errors'       => $v->errors()->toArray(),
                    'reason'       => 'validation_error',
                    'role'         => 'employee',
                ],
                request: $request
            );

            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }

        $data = $v->validated();
        $password = !empty($data['password']) ? (string) $data['password'] : Str::random(32);

        if (DB::table('users')->where('email', $data['email'])->whereNull('deleted_at')->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email already exists',
            ], 422);
        }

        if (DB::table('users')->where('phone_number', $data['phone_number'])->whereNull('deleted_at')->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Phone number already exists',
            ], 422);
        }

        do {
            $uuid = (string) Str::uuid();
        } while (DB::table('users')->where('uuid', $uuid)->exists());

        $name = trim((string) $data['name']);
        $base = Str::slug($name ?: 'employee');

        do {
            $slug = $base . '-' . Str::lower(Str::random(24));
        } while (DB::table('users')->where('slug', $slug)->exists());

        [$role, $roleShort] = $this->normalizeRole($data['role'] ?? 'employee', null);

        $now = now();
        $metadata = [
            'timezone' => 'Asia/Kolkata',
            'source'   => (string) ($data['source'] ?? 'employee_register_api'),
        ];
        $successMessage = 'Account created successfully';

        try {
            DB::table('users')->insert([
                'uuid'            => $uuid,
                'name'            => $name,
                'email'           => $data['email'],
                'phone_number'    => $data['phone_number'],
                'password'        => Hash::make($password),
                'role'            => $role,
                'role_short_form' => $roleShort,
                'slug'            => $slug,
                'status'          => 'active',
                'remember_token'  => Str::random(60),
                'created_by'      => null,
                'created_at'      => $now,
                'created_at_ip'   => $request->ip(),
                'updated_at'      => $now,
                'metadata'        => json_encode($metadata, JSON_UNESCAPED_UNICODE),
            ]);

            $user       = DB::table('users')->where('email', $data['email'])->first();
            $expiresAt  = now()->addDays(30);
            $plainToken = $this->issueToken((int) $user->id, $expiresAt);

            $this->logActivity(
                activity: 'store',
                title: 'Registration successful',
                description: 'A new account was registered successfully.',
                performedBy: (int) $user->id,
                performedByName: $user->name ?? null,
                targetId: $user->id,
                targetType: 'user',
                properties: [
                    'name'           => $name,
                    'email'          => $data['email'],
                    'phone_number'   => $data['phone_number'],
                    'role'           => $role,
                    'expires_at'     => $expiresAt->toIso8601String(),
                ],
                request: $request
            );

            $this->notifyAdmins(
                'Employee account registered',
                ($user->name ?? $name) . ' registered successfully.',
                [
                    'action' => 'employee_registered',
                    'module' => 'users',
                    'user'   => [
                        'id'    => (int) $user->id,
                        'uuid'  => (string) ($user->uuid ?? ''),
                        'name'  => (string) ($user->name ?? $name),
                        'email' => (string) ($user->email ?? $data['email']),
                        'role'  => (string) ($user->role ?? $role),
                    ],
                ],
                $this->manageUsersLink((int) $user->id),
                'user'
            );

            return response()->json([
                'status'       => 'success',
                'message'      => $successMessage,
                'access_token' => $plainToken,
                'token_type'   => 'Bearer',
                'expires_at'   => $expiresAt->toIso8601String(),
                'user'         => $this->publicUserPayload($user),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('[Register] failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Account registration failed',
            ], 500);
        }
    }

    /**
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $plain = $this->extractToken($request);

        if (!$plain) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Token not provided',
            ], 401);
        }

        $hashedToken = hash('sha256', $plain);

        $tokenRow = DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->where('tokenable_type', self::USER_TYPE)
            ->first();

        $user = null;
        if ($tokenRow && !empty($tokenRow->tokenable_id)) {
            $user = DB::table('users')->where('id', $tokenRow->tokenable_id)->first();
        }

        $deleted = DB::table('personal_access_tokens')
            ->where('token', $hashedToken)
            ->where('tokenable_type', self::USER_TYPE)
            ->delete();

        if ($deleted) {
            $this->logActivity(
                activity: 'logout',
                title: 'User logout successful',
                description: 'User logged out successfully.',
                performedBy: $user?->id ? (int) $user->id : 0,
                performedByName: $user?->name ?? null,
                targetId: $user?->id ?? null,
                targetType: 'user',
                properties: [
                    'email'  => $user?->email ?? null,
                    'reason' => 'token_revoked',
                    'role'   => $user?->role ?? null,
                ],
                request: $request
            );
        }

        return response()->json([
            'status'  => $deleted ? 'success' : 'error',
            'message' => $deleted ? 'Logged out successfully' : 'Invalid token',
        ], $deleted ? 200 : 401);
    }

    /**
     * GET /api/auth/my-role
     */
    public function getMyRole(Request $request)
    {
        $plain = $this->extractToken($request);
        if (!$plain) {
            return response()->json(['status' => 'error', 'message' => 'Token not provided'], 401);
        }

        $rec = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $plain))
            ->where('tokenable_type', self::USER_TYPE)
            ->first();

        if (!$rec) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        if (!empty($rec->expires_at) && Carbon::parse($rec->expires_at)->isPast()) {
            DB::table('personal_access_tokens')->where('id', $rec->id)->delete();
            return response()->json(['status' => 'error', 'message' => 'Token expired'], 401);
        }

        $user = DB::table('users')
            ->where('id', $rec->tokenable_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || (isset($user->status) && $user->status !== 'active')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'status'          => 'success',
            'role'            => (string) ($user->role ?? ''),
            'role_short_form' => (string) ($user->role_short_form ?? ''),
            'user'            => $this->publicUserPayload($user),
        ]);
    }

    /**
     * GET /api/auth/check
     */
    public function authenticateToken(Request $request)
    {
        $plain = $this->extractToken($request);
        if (!$plain) {
            return response()->json(['status' => 'error', 'message' => 'Token not provided'], 401);
        }

        $rec = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $plain))
            ->where('tokenable_type', self::USER_TYPE)
            ->first();

        if (!$rec) {
            return response()->json(['status' => 'error', 'message' => 'Invalid token'], 401);
        }

        if (!empty($rec->expires_at) && Carbon::parse($rec->expires_at)->isPast()) {
            DB::table('personal_access_tokens')->where('id', $rec->id)->delete();
            return response()->json(['status' => 'error', 'message' => 'Token expired'], 401);
        }

        $user = DB::table('users')
            ->where('id', $rec->tokenable_id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || (isset($user->status) && $user->status !== 'active')) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'status' => 'success',
            'user'   => $this->publicUserPayload($user),
        ]);
    }

    /* =========================================================
     |                       USERS CRUD
     |=========================================================*/

    /**
     * POST /api/users
     */
    public function store(Request $request)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'name'                     => 'required|string|max:150',
            'email'                    => 'required|email|max:255',
            'password'                 => 'required|string|min:8',
            'phone_number'             => 'sometimes|nullable|string|max:32',
            'alternative_email'        => 'sometimes|nullable|email|max:255',
            'alternative_phone_number' => 'sometimes|nullable|string|max:32',
            'whatsapp_number'          => 'sometimes|nullable|string|max:32',
            'address'                  => 'sometimes|nullable|string',
            'role'                     => 'sometimes|nullable|string|max:50',
            'role_short_form'          => 'sometimes|nullable|string|max:10',
            'status'                   => 'sometimes|in:active,inactive',
            'image'                    => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        if (DB::table('users')->where('email', $data['email'])->whereNull('deleted_at')->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Email already exists'], 422);
        }

        if (!empty($data['phone_number']) &&
            DB::table('users')->where('phone_number', $data['phone_number'])->whereNull('deleted_at')->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Phone number already exists'], 422);
        }

        do {
            $uuid = (string) Str::uuid();
        } while (DB::table('users')->where('uuid', $uuid)->exists());

        $base = Str::slug((string) $data['name']);
        do {
            $slug = $base . '-' . Str::lower(Str::random(24));
        } while (DB::table('users')->where('slug', $slug)->exists());

        [$role, $roleShort] = $this->normalizeRole(
            $data['role'] ?? 'employee',
            $data['role_short_form'] ?? null
        );

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->saveProfileImage($request->file('image'));
            if ($imageUrl === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }
        }

        $createdBy = $this->currentUserId($request);

        try {
            $now = now();

            DB::table('users')->insert([
                'uuid'                     => $uuid,
                'name'                     => $data['name'],
                'email'                    => $data['email'],
                'phone_number'             => $data['phone_number'] ?? null,
                'alternative_email'        => $data['alternative_email'] ?? null,
                'alternative_phone_number' => $data['alternative_phone_number'] ?? null,
                'whatsapp_number'          => $data['whatsapp_number'] ?? null,
                'password'                 => Hash::make($data['password']),
                'image'                    => $imageUrl,
                'address'                  => $data['address'] ?? null,
                'role'                     => $role,
                'role_short_form'          => $roleShort,
                'slug'                     => $slug,
                'status'                   => $data['status'] ?? 'active',
                'remember_token'           => Str::random(60),
                'created_by'               => $createdBy,
                'created_at'               => $now,
                'created_at_ip'            => $request->ip(),
                'updated_at'               => $now,
                'metadata'                 => json_encode([
                    'timezone' => 'Asia/Kolkata',
                    'source'   => 'user_store_api',
                ], JSON_UNESCAPED_UNICODE),
            ]);

            $user = DB::table('users')->where('email', $data['email'])->first();

            $this->logActivity(
                activity: 'store',
                title: 'User created',
                description: 'A new user account was created.',
                performedBy: $actor['id'] ?: (int) $user->id,
                performedByName: $actor['name'] ?? ($user->name ?? null),
                targetId: $user->id,
                targetType: 'user',
                properties: [
                    'role'         => $actor['role'],
                    'target_role'  => $user->role ?? $role,
                    'name'         => $user->name ?? $data['name'],
                    'email'        => $user->email ?? $data['email'],
                    'phone_number' => $user->phone_number ?? ($data['phone_number'] ?? null),
                    'status'       => $user->status ?? ($data['status'] ?? 'active'),
                ],
                request: $request
            );

            $this->notifyAdmins(
                'User created',
                ($user->name ?? $data['name']) . ' was created successfully.',
                [
                    'action'     => 'created',
                    'module'     => 'users',
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'user'       => [
                        'id'     => (int) $user->id,
                        'uuid'   => (string) ($user->uuid ?? ''),
                        'name'   => (string) ($user->name ?? $data['name']),
                        'email'  => (string) ($user->email ?? $data['email']),
                        'role'   => (string) ($user->role ?? $role),
                        'status' => (string) ($user->status ?? ($data['status'] ?? 'active')),
                    ],
                ],
                $this->manageUsersLink((int) $user->id),
                'user'
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'User created',
                'user'    => $this->publicUserPayload($user),
            ], 201);
        } catch (\Throwable $e) {
            if ($imageUrl) {
                $this->deleteManagedProfileImage($imageUrl);
            }

            Log::error('[Users Store] failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Could not create user',
            ], 500);
        }
    }

    /**
     * POST /api/users/{uuid}/cv
     */
    public function uploadCvByUuid(Request $request, string $uuid)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'cv' => 'required|file|max:10240|mimes:pdf,doc,docx',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $user = DB::table('users')
            ->where('uuid', $uuid)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $file = $request->file('cv');
        if (!$file || !$file->isValid()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid CV upload'], 422);
        }

        $destDir = public_path('assets/images/usercv');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext      = strtolower($file->getClientOriginalExtension() ?: 'bin');
        $filename = 'cv_' . date('Ymd_His') . '_' . Str::lower(Str::random(18)) . '.' . $ext;

        try {
            DB::beginTransaction();

            $locked = DB::table('users')
                ->where('id', $user->id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$locked) {
                DB::rollBack();
                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            $file->move($destDir, $filename);
            $relativePath = '/assets/images/usercv/' . $filename;
            $oldCv        = $locked->cv ?? null;

            DB::table('users')->where('id', $locked->id)->update([
                'cv'         => $relativePath,
                'updated_at' => now(),
            ]);

            DB::commit();

            if (!empty($oldCv)) {
                $this->deleteManagedCv($oldCv);
            }

            $fresh = DB::table('users')->where('id', $locked->id)->first();

            $this->logActivity(
                activity: 'upload_cv',
                title: 'User CV uploaded',
                description: 'A user CV was uploaded or replaced.',
                performedBy: $actor['id'] ?: (int) $fresh->id,
                performedByName: $actor['name'] ?? ($fresh->name ?? null),
                targetId: $fresh->id,
                targetType: 'user',
                properties: [
                    'role'          => $actor['role'],
                    'target_role'   => $fresh->role ?? null,
                    'cv_path'       => (string) ($fresh->cv ?? ''),
                    'replaced_old'  => !empty($oldCv),
                ],
                request: $request
            );

            $this->notifyAdmins(
                'User CV updated',
                ($fresh->name ?? 'A user') . '\'s CV was uploaded.',
                [
                    'action'     => 'upload_cv',
                    'module'     => 'users',
                    'actor_id'   => $actor['id'],
                    'actor_role' => $actor['role'],
                    'user'       => [
                        'id'   => (int) $fresh->id,
                        'uuid' => (string) ($fresh->uuid ?? ''),
                        'name' => (string) ($fresh->name ?? ''),
                        'role' => (string) ($fresh->role ?? ''),
                    ],
                ],
                $this->manageUsersLink((int) $fresh->id),
                'user'
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'CV uploaded successfully',
                'data'    => [
                    'user_id' => (int) $fresh->id,
                    'uuid'    => (string) ($fresh->uuid ?? ''),
                    'cv'      => $this->publicFileUrl($fresh->cv ?? null),
                    'cv_path' => (string) ($fresh->cv ?? ''),
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            $maybeAbs = $destDir . DIRECTORY_SEPARATOR . $filename;
            if (File::exists($maybeAbs)) {
                @File::delete($maybeAbs);
            }

            Log::error('[Upload CV] failed', [
                'uuid'  => $uuid,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to upload CV',
            ], 500);
        }
    }

    /**
     * GET /api/users/all
     */
    public function all(Request $request)
    {
        $q      = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', 'active');
        $limit  = min(1000, max(1, (int) $request->query('limit', 1000)));

        $rows = DB::table('users')
            ->whereNull('deleted_at')
            ->when($status !== '', fn($w) => $w->where('status', $status))
            ->when($q !== '', function ($w) use ($q) {
                $like = "%{$q}%";
                $w->where(function ($x) use ($like) {
                    $x->where('name', 'LIKE', $like)
                      ->orWhere('email', 'LIKE', $like)
                      ->orWhere('phone_number', 'LIKE', $like);
                });
            })
            ->select('id', 'uuid', 'name', 'email', 'phone_number', 'image', 'role', 'role_short_form', 'status')
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $row->image = $this->publicImageUrl($row->image ?? null);
                return $row;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $rows,
            'meta'   => ['count' => $rows->count()],
        ]);
    }

    /**
     * GET /api/users
     */
    public function index(Request $request)
    {
        $page   = max(1, (int) $request->query('page', 1));
        $pp     = min(100, max(1, (int) $request->query('per_page', 20)));
        $q      = trim((string) $request->query('q', ''));
        $status = $request->has('status') ? (string) $request->query('status') : 'active';
        $role   = trim((string) $request->query('role', ''));

        $base = DB::table('users')->whereNull('deleted_at');

        if ($status !== 'all' && $status !== '') {
            $base->where('status', $status);
        }

        if ($q !== '') {
            $like = "%{$q}%";
            $base->where(function ($w) use ($like) {
                $w->where('name', 'LIKE', $like)
                  ->orWhere('email', 'LIKE', $like)
                  ->orWhere('phone_number', 'LIKE', $like);
            });
        }

        if ($role !== '' && $role !== 'all') {
            $base->where('role', $role);
        }

        $total = (clone $base)->count();

        $rows = $base->orderBy('name')
            ->offset(($page - 1) * $pp)
            ->limit($pp)
            ->select('id', 'uuid', 'cv', 'name', 'email', 'phone_number', 'image', 'role', 'role_short_form', 'status')
            ->get()
            ->map(function ($row) {
                $row->image = $this->publicImageUrl($row->image ?? null);
                $row->cv    = $this->publicFileUrl($row->cv ?? null);
                return $row;
            });

        return response()->json([
            'status' => 'success',
            'data'   => $rows,
            'meta'   => [
                'page'        => $page,
                'per_page'    => $pp,
                'total'       => $total,
                'total_pages' => (int) ceil($total / $pp),
            ],
        ]);
    }

    /**
     * GET /api/users/{id}
     */
    public function show(Request $request, int $id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->whereNull('deleted_at')
            ->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'user'   => [
                'id'                       => (int) $user->id,
                'uuid'                     => $user->uuid,
                'name'                     => $user->name,
                'email'                    => $user->email,
                'phone_number'             => $user->phone_number,
                'alternative_email'        => $user->alternative_email,
                'alternative_phone_number' => $user->alternative_phone_number,
                'whatsapp_number'          => $user->whatsapp_number,
                'image'                    => $this->publicImageUrl($user->image ?? null),
                'cv'                       => $this->publicFileUrl($user->cv ?? null),
                'address'                  => $user->address,
                'role'                     => $user->role,
                'role_short_form'          => $user->role_short_form,
                'slug'                     => $user->slug,
                'status'                   => $user->status,
                'last_login_at'            => $user->last_login_at,
                'last_login_ip'            => $user->last_login_ip,
                'created_by'               => $user->created_by,
                'created_at'               => $user->created_at,
                'updated_at'               => $user->updated_at,
                'deleted_at'               => $user->deleted_at,
            ],
        ]);
    }

    /**
     * PUT/PATCH /api/users/{id}
     */
    public function update(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'name'                     => 'sometimes|string|max:150',
            'email'                    => 'sometimes|email|max:255',
            'phone_number'             => 'sometimes|nullable|string|max:32',
            'alternative_email'        => 'sometimes|nullable|email|max:255',
            'alternative_phone_number' => 'sometimes|nullable|string|max:32',
            'whatsapp_number'          => 'sometimes|nullable|string|max:32',
            'address'                  => 'sometimes|nullable|string',
            'role'                     => 'sometimes|nullable|string|max:50',
            'role_short_form'          => 'sometimes|nullable|string|max:10',
            'status'                   => 'sometimes|in:active,inactive',
            'image'                    => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $data = $v->validated();

        $existing = DB::table('users')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$existing) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        if (array_key_exists('email', $data)) {
            if (DB::table('users')
                ->where('email', $data['email'])
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Email already exists'], 422);
            }
        }

        if (array_key_exists('phone_number', $data) && !empty($data['phone_number'])) {
            if (DB::table('users')
                ->where('phone_number', $data['phone_number'])
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Phone number already exists'], 422);
            }
        }

        $updates = [];
        foreach ([
            'name',
            'email',
            'phone_number',
            'alternative_email',
            'alternative_phone_number',
            'whatsapp_number',
            'address',
            'status',
        ] as $key) {
            if (array_key_exists($key, $data)) {
                $updates[$key] = $data[$key];
            }
        }

        if (array_key_exists('role', $data) || array_key_exists('role_short_form', $data)) {
            [$normRole, $normShort] = $this->normalizeRole(
                $data['role'] ?? $existing->role,
                $data['role_short_form'] ?? $existing->role_short_form
            );

            $updates['role']            = $normRole;
            $updates['role_short_form'] = $normShort;
        }

        if (array_key_exists('name', $updates) && $updates['name'] !== $existing->name) {
            $base = Str::slug((string) $updates['name']);
            do {
                $slug = $base . '-' . Str::lower(Str::random(24));
            } while (DB::table('users')->where('slug', $slug)->where('id', '!=', $id)->exists());

            $updates['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            $newUrl = $this->saveProfileImage($request->file('image'));
            if ($newUrl === false) {
                return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
            }

            $this->deleteManagedProfileImage($existing->image);
            $updates['image'] = $newUrl;
        }

        if (empty($updates)) {
            return response()->json(['status' => 'error', 'message' => 'Nothing to update'], 400);
        }

        $updates['updated_at'] = now();

        DB::table('users')->where('id', $id)->update($updates);

        $fresh = DB::table('users')->where('id', $id)->first();

        $changedKeys = array_values(array_filter(array_keys($updates), fn ($key) => $key !== 'updated_at'));

        $this->logActivity(
            activity: 'update',
            title: 'User updated',
            description: 'A user account was updated.',
            performedBy: $actor['id'] ?: (int) $fresh->id,
            performedByName: $actor['name'] ?? ($fresh->name ?? null),
            targetId: $fresh->id,
            targetType: 'user',
            properties: [
                'role'          => $actor['role'],
                'target_role'   => $fresh->role ?? null,
                'changed_fields'=> $changedKeys,
                'email'         => $fresh->email ?? null,
                'status'        => $fresh->status ?? null,
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User updated',
            ($fresh->name ?? 'A user') . ' was updated.',
            [
                'action'        => 'updated',
                'module'        => 'users',
                'actor_id'      => $actor['id'],
                'actor_role'    => $actor['role'],
                'changed_fields'=> $changedKeys,
                'user'          => [
                    'id'     => (int) $fresh->id,
                    'uuid'   => (string) ($fresh->uuid ?? ''),
                    'name'   => (string) ($fresh->name ?? ''),
                    'email'  => (string) ($fresh->email ?? ''),
                    'role'   => (string) ($fresh->role ?? ''),
                    'status' => (string) ($fresh->status ?? ''),
                ],
            ],
            $this->manageUsersLink((int) $fresh->id),
            'user'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated',
            'user'    => $this->publicUserPayload($fresh),
        ]);
    }

    /**
     * DELETE /api/users/{id}
     * Soft delete
     */
    public function destroy(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $actorId = $this->currentUserId($request);
        if ($actorId !== null && $actorId === $id) {
            return response()->json(['status' => 'error', 'message' => "You can't delete your own account"], 422);
        }

        $user = DB::table('users')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        DB::table('users')->where('id', $id)->update([
            'deleted_at' => now(),
            'status'     => 'inactive',
            'updated_at' => now(),
        ]);

        $this->logActivity(
            activity: 'destroy',
            title: 'User soft deleted',
            description: 'A user account was soft deleted.',
            performedBy: $actor['id'] ?: (int) $user->id,
            performedByName: $actor['name'] ?? ($user->name ?? null),
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'role'        => $actor['role'],
                'target_role' => $user->role ?? null,
                'email'       => $user->email ?? null,
                'status'      => 'inactive',
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User deleted',
            ($user->name ?? 'A user') . ' was moved to inactive/deleted state.',
            [
                'action'     => 'soft_deleted',
                'module'     => 'users',
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'user'       => [
                    'id'    => (int) $user->id,
                    'uuid'  => (string) ($user->uuid ?? ''),
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role'  => (string) ($user->role ?? ''),
                ],
            ],
            $this->manageUsersLink((int) $user->id),
            'user'
        );

        return response()->json(['status' => 'success', 'message' => 'User soft-deleted']);
    }

    /**
     * POST /api/users/{id}/restore
     */
    public function restore(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $user = DB::table('users')->where('id', $id)->whereNotNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found or not deleted'], 404);
        }

        DB::table('users')->where('id', $id)->update([
            'deleted_at' => null,
            'status'     => 'active',
            'updated_at' => now(),
        ]);

        $this->logActivity(
            activity: 'restore',
            title: 'User restored',
            description: 'A deleted user account was restored.',
            performedBy: $actor['id'] ?: (int) $user->id,
            performedByName: $actor['name'] ?? ($user->name ?? null),
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'role'        => $actor['role'],
                'target_role' => $user->role ?? null,
                'email'       => $user->email ?? null,
                'status'      => 'active',
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User restored',
            ($user->name ?? 'A user') . ' was restored.',
            [
                'action'     => 'restored',
                'module'     => 'users',
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'user'       => [
                    'id'    => (int) $user->id,
                    'uuid'  => (string) ($user->uuid ?? ''),
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role'  => (string) ($user->role ?? ''),
                ],
            ],
            $this->manageUsersLink((int) $user->id),
            'user'
        );

        return response()->json(['status' => 'success', 'message' => 'User restored']);
    }

    /**
     * DELETE /api/users/{id}/force
     */
    public function forceDelete(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $actorId = $this->currentUserId($request);
        if ($actorId !== null && $actorId === $id) {
            return response()->json(['status' => 'error', 'message' => "You can't delete your own account"], 422);
        }

        $user = DB::table('users')->where('id', $id)->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $this->deleteManagedProfileImage($user->image ?? null);
        $this->deleteManagedCv($user->cv ?? null);

        DB::table('users')->where('id', $id)->delete();

        $this->logActivity(
            activity: 'force_delete',
            title: 'User permanently deleted',
            description: 'A user account was permanently deleted.',
            performedBy: $actor['id'] ?: (int) $user->id,
            performedByName: $actor['name'] ?? ($user->name ?? null),
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'role'        => $actor['role'],
                'target_role' => $user->role ?? null,
                'email'       => $user->email ?? null,
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User permanently deleted',
            ($user->name ?? 'A user') . ' was permanently deleted.',
            [
                'action'     => 'force_deleted',
                'module'     => 'users',
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'user'       => [
                    'id'    => (int) $user->id,
                    'uuid'  => (string) ($user->uuid ?? ''),
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role'  => (string) ($user->role ?? ''),
                ],
            ],
            '/user/manage',
            'user',
            'high'
        );

        return response()->json(['status' => 'success', 'message' => 'User permanently deleted']);
    }

    /**
     * PATCH /api/users/{id}/password
     */
    public function updatePassword(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $user = DB::table('users')->where('id', $id)->whereNull('deleted_at')->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        DB::table('users')->where('id', $id)->update([
            'password'   => Hash::make($v->validated()['password']),
            'updated_at' => now(),
        ]);

        $this->logActivity(
            activity: 'update_password',
            title: 'User password updated',
            description: 'A user password was updated by an authorized actor.',
            performedBy: $actor['id'] ?: (int) $user->id,
            performedByName: $actor['name'] ?? ($user->name ?? null),
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'role'        => $actor['role'],
                'target_role' => $user->role ?? null,
                'email'       => $user->email ?? null,
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User password updated',
            'Password was updated for ' . ($user->name ?? 'a user') . '.',
            [
                'action'     => 'password_updated',
                'module'     => 'users',
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'user'       => [
                    'id'    => (int) $user->id,
                    'uuid'  => (string) ($user->uuid ?? ''),
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role'  => (string) ($user->role ?? ''),
                ],
            ],
            $this->manageUsersLink((int) $user->id),
            'user',
            'high'
        );

        return response()->json(['status' => 'success', 'message' => 'Password updated']);
    }

    /**
     * POST /api/users/{id}/image
     */
    public function updateImage(Request $request, int $id)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'image' => 'required|file|max:5120|mimes:jpg,jpeg,png,webp,gif,svg',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $file = $request->file('image');
        if (!$file || !$file->isValid()) {
            return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
        }

        $newUrl = $this->saveProfileImage($file);
        if ($newUrl === false) {
            return response()->json(['status' => 'error', 'message' => 'Invalid image upload'], 422);
        }

        $oldUrl = null;

        try {
            DB::beginTransaction();

            $user = DB::table('users')
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();

            if (!$user) {
                DB::rollBack();
                $this->deleteManagedProfileImage($newUrl);

                return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
            }

            $oldUrl = $user->image;

            DB::table('users')->where('id', $id)->update([
                'image'      => $newUrl,
                'updated_at' => now(),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->deleteManagedProfileImage($newUrl);

            report($e);

            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to update image. Please try again.',
            ], 500);
        }

        if (!empty($oldUrl)) {
            $this->deleteManagedProfileImage($oldUrl);
        }

        $fresh = DB::table('users')->where('id', $id)->first();

        $this->logActivity(
            activity: 'update_image',
            title: 'User image updated',
            description: 'A user profile image was updated.',
            performedBy: $actor['id'] ?: (int) $fresh->id,
            performedByName: $actor['name'] ?? ($fresh->name ?? null),
            targetId: $fresh->id,
            targetType: 'user',
            properties: [
                'role'          => $actor['role'],
                'target_role'   => $fresh->role ?? null,
                'email'         => $fresh->email ?? null,
                'replaced_old'  => !empty($oldUrl),
            ],
            request: $request
        );

        $this->notifyAdmins(
            'User image updated',
            ($fresh->name ?? 'A user') . '\'s profile image was updated.',
            [
                'action'     => 'image_updated',
                'module'     => 'users',
                'actor_id'   => $actor['id'],
                'actor_role' => $actor['role'],
                'user'       => [
                    'id'    => (int) $fresh->id,
                    'uuid'  => (string) ($fresh->uuid ?? ''),
                    'name'  => (string) ($fresh->name ?? ''),
                    'email' => (string) ($fresh->email ?? ''),
                    'role'  => (string) ($fresh->role ?? ''),
                ],
            ],
            $this->manageUsersLink((int) $fresh->id),
            'user'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Image updated',
            'user'    => $this->publicUserPayload($fresh),
        ]);
    }

    /**
     * POST /api/users/import-csv
     */
    public function importUsersCsv(Request $request)
    {
        $actor = $this->currentActorContext($request);

        $v = Validator::make($request->all(), [
            'file'             => 'required|file|max:10240|mimes:csv,txt',
            'default_password' => 'sometimes|nullable|string|min:6|max:100',
            'default_role'     => 'sometimes|nullable|string|max:50',
        ]);

        if ($v->fails()) {
            return response()->json(['status' => 'error', 'errors' => $v->errors()], 422);
        }

        $file = $request->file('file');

        $defaultPassword = (string) ($request->input('default_password') ?: 'Employee@123');
        $defaultRoleIn   = (string) ($request->input('default_role') ?: 'employee');
        [$defaultRole, $defaultRoleShort] = $this->normalizeRole($defaultRoleIn, null);

        $path = $file->getRealPath();
        if (!$path || !file_exists($path)) {
            return response()->json(['status' => 'error', 'message' => 'Uploaded file not found'], 422);
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            return response()->json(['status' => 'error', 'message' => 'Unable to read CSV'], 422);
        }

        $header = fgetcsv($handle);
        if (!$header || !is_array($header)) {
            fclose($handle);
            return response()->json(['status' => 'error', 'message' => 'CSV header missing'], 422);
        }

        $header = array_map(function ($h) {
            $h = strtolower(trim((string) $h));
            return preg_replace('/\s+/', '_', $h);
        }, $header);

        foreach (['name', 'email'] as $req) {
            if (!in_array($req, $header, true)) {
                fclose($handle);
                return response()->json([
                    'status'  => 'error',
                    'message' => "CSV must contain '{$req}' column in header",
                ], 422);
            }
        }

        $actorId = $this->currentUserId($request);
        $now     = now();

        $imported = 0;
        $skipped  = 0;
        $errors   = [];

        DB::beginTransaction();

        try {
            $rowIndex = 1;

            while (($data = fgetcsv($handle)) !== false) {
                $rowIndex++;

                if (!is_array($data) || count(array_filter($data, fn($x) => trim((string) $x) !== '')) === 0) {
                    continue;
                }

                $row = [];
                foreach ($header as $i => $key) {
                    $row[$key] = $data[$i] ?? null;
                }

                $name     = trim((string) ($row['name'] ?? ''));
                $email    = trim((string) ($row['email'] ?? ''));
                $password = (string) ($row['password'] ?? '');
                $roleIn   = (string) ($row['role'] ?? '');

                $phoneNumber             = trim((string) ($row['phone_number'] ?? ''));
                $alternativeEmail        = trim((string) ($row['alternative_email'] ?? ''));
                $alternativePhoneNumber  = trim((string) ($row['alternative_phone_number'] ?? ''));
                $whatsappNumber          = trim((string) ($row['whatsapp_number'] ?? ''));
                $address                 = trim((string) ($row['address'] ?? ''));

                if ($name === '' || $email === '') {
                    $skipped++;
                    $errors[] = "Row {$rowIndex}: name/email missing";
                    continue;
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $skipped++;
                    $errors[] = "Row {$rowIndex}: invalid email {$email}";
                    continue;
                }

                if (DB::table('users')->where('email', $email)->whereNull('deleted_at')->exists()) {
                    $skipped++;
                    $errors[] = "Row {$rowIndex}: email already exists {$email}";
                    continue;
                }

                if ($phoneNumber !== '' &&
                    DB::table('users')->where('phone_number', $phoneNumber)->whereNull('deleted_at')->exists()) {
                    $skipped++;
                    $errors[] = "Row {$rowIndex}: phone number already exists {$phoneNumber}";
                    continue;
                }

                $finalPassword = trim($password) !== '' ? $password : $defaultPassword;

                if (trim($roleIn) !== '') {
                    [$role, $roleShort] = $this->normalizeRole($roleIn, null);
                } else {
                    $role      = $defaultRole;
                    $roleShort = $defaultRoleShort;
                }

                do {
                    $uuid = (string) Str::uuid();
                } while (DB::table('users')->where('uuid', $uuid)->exists());

                $base = Str::slug($name ?: 'user');
                do {
                    $slug = $base . '-' . Str::lower(Str::random(24));
                } while (DB::table('users')->where('slug', $slug)->exists());

                DB::table('users')->insert([
                    'uuid'                     => $uuid,
                    'name'                     => $name,
                    'email'                    => $email,
                    'phone_number'             => $phoneNumber !== '' ? $phoneNumber : null,
                    'alternative_email'        => $alternativeEmail !== '' ? $alternativeEmail : null,
                    'alternative_phone_number' => $alternativePhoneNumber !== '' ? $alternativePhoneNumber : null,
                    'whatsapp_number'          => $whatsappNumber !== '' ? $whatsappNumber : null,
                    'address'                  => $address !== '' ? $address : null,
                    'password'                 => Hash::make($finalPassword),
                    'role'                     => $role,
                    'role_short_form'          => $roleShort,
                    'slug'                     => $slug,
                    'status'                   => 'active',
                    'remember_token'           => Str::random(60),
                    'created_by'               => $actorId,
                    'created_at'               => $now,
                    'created_at_ip'            => $request->ip(),
                    'updated_at'               => $now,
                    'metadata'                 => json_encode([
                        'timezone' => 'Asia/Kolkata',
                        'source'   => 'users_csv_import',
                    ], JSON_UNESCAPED_UNICODE),
                ]);

                $imported++;
            }

            DB::commit();
            fclose($handle);

            $this->logActivity(
                activity: 'import_csv',
                title: 'Users CSV import completed',
                description: 'Bulk user import from CSV finished.',
                performedBy: $actor['id'],
                performedByName: $actor['name'],
                targetId: $actor['id'] ?: null,
                targetType: 'users_csv_import',
                properties: [
                    'role'          => $actor['role'],
                    'imported'      => $imported,
                    'skipped'       => $skipped,
                    'default_role'  => $defaultRole,
                    'error_count'   => count($errors),
                ],
                request: $request
            );

            $this->notifyAdmins(
                'Users CSV import completed',
                $imported . ' user(s) were imported from CSV.',
                [
                    'action'      => 'import_csv',
                    'module'      => 'users',
                    'actor_id'    => $actor['id'],
                    'actor_role'  => $actor['role'],
                    'imported'    => $imported,
                    'skipped'     => $skipped,
                    'error_count' => count($errors),
                    'default_role'=> $defaultRole,
                ],
                '/user/manage',
                'user'
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'CSV import completed',
                'meta'    => [
                    'imported' => $imported,
                    'skipped'  => $skipped,
                    'errors'   => $errors,
                ],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            fclose($handle);

            Log::error('[Users CSV Import] failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status'  => 'error',
                'message' => 'CSV import failed',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/profile
     */
    public function updateMyProfile(Request $request)
    {
        $actor = $this->currentActorContext($request);

        $authId = $request->attributes->get('auth_user_id')
            ?? $request->attributes->get('auth_tokenable_id')
            ?? $this->currentUserId($request);

        if (!$authId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $v = Validator::make($request->all(), [
            'name'                     => 'sometimes|string|max:150',
            'email'                    => 'sometimes|email|max:255',
            'phone_number'             => 'sometimes|nullable|string|max:32',
            'alternative_email'        => 'sometimes|nullable|email|max:255',
            'alternative_phone_number' => 'sometimes|nullable|string|max:32',
            'whatsapp_number'          => 'sometimes|nullable|string|max:32',
            'address'                  => 'sometimes|nullable|string',
            'image'                    => 'sometimes|file|mimes:jpg,jpeg,png,webp,gif,svg|max:5120',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }

        $data = $v->validated();

        $existing = DB::table('users')
            ->where('id', (int) $authId)
            ->whereNull('deleted_at')
            ->first();

        if (!$existing || (isset($existing->status) && $existing->status !== 'active')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        if (array_key_exists('email', $data)) {
            if (DB::table('users')
                ->where('email', $data['email'])
                ->where('id', '!=', (int) $authId)
                ->whereNull('deleted_at')
                ->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Email already exists',
                ], 422);
            }
        }

        if (array_key_exists('phone_number', $data) && !empty($data['phone_number'])) {
            if (DB::table('users')
                ->where('phone_number', $data['phone_number'])
                ->where('id', '!=', (int) $authId)
                ->whereNull('deleted_at')
                ->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Phone number already exists',
                ], 422);
            }
        }

        $updates = [];
        foreach ([
            'name',
            'email',
            'phone_number',
            'alternative_email',
            'alternative_phone_number',
            'whatsapp_number',
            'address',
        ] as $key) {
            if (array_key_exists($key, $data)) {
                $updates[$key] = $data[$key];
            }
        }

        if (array_key_exists('name', $updates) && $updates['name'] !== $existing->name) {
            $base = Str::slug((string) $updates['name']);
            do {
                $slug = $base . '-' . Str::lower(Str::random(24));
            } while (DB::table('users')->where('slug', $slug)->where('id', '!=', (int) $authId)->exists());

            $updates['slug'] = $slug;
        }

        $oldImage = null;
        if ($request->hasFile('image')) {
            $newUrl = $this->saveProfileImage($request->file('image'));
            if ($newUrl === false) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Invalid image upload',
                ], 422);
            }

            $updates['image'] = $newUrl;
            $oldImage = $existing->image ?? null;
        }

        if (empty($updates)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Nothing to update',
            ], 400);
        }

        $updates['updated_at'] = now();

        DB::table('users')->where('id', (int) $authId)->update($updates);

        if ($oldImage && array_key_exists('image', $updates)) {
            $this->deleteManagedProfileImage($oldImage);
        }

        $fresh = DB::table('users')->where('id', (int) $authId)->first();

        $changedKeys = array_values(array_filter(array_keys($updates), fn ($key) => $key !== 'updated_at'));

        $this->logActivity(
            activity: 'update_profile',
            title: 'Profile updated',
            description: 'A user updated their own profile.',
            performedBy: $actor['id'] ?: (int) $fresh->id,
            performedByName: $actor['name'] ?? ($fresh->name ?? null),
            targetId: $fresh->id,
            targetType: 'user',
            properties: [
                'role'          => $actor['role'] ?? ($fresh->role ?? null),
                'changed_fields'=> $changedKeys,
                'email'         => $fresh->email ?? null,
            ],
            request: $request,
            module: 'profile'
        );

        $this->notifyAdmins(
            'Profile updated',
            ($fresh->name ?? 'A user') . ' updated their profile.',
            [
                'action'        => 'profile_updated',
                'module'        => 'profile',
                'actor_id'      => $actor['id'] ?: (int) $fresh->id,
                'actor_role'    => $actor['role'] ?? ($fresh->role ?? null),
                'changed_fields'=> $changedKeys,
                'user'          => [
                    'id'    => (int) $fresh->id,
                    'uuid'  => (string) ($fresh->uuid ?? ''),
                    'name'  => (string) ($fresh->name ?? ''),
                    'email' => (string) ($fresh->email ?? ''),
                    'role'  => (string) ($fresh->role ?? ''),
                ],
            ],
            '/profile',
            'profile'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
            'user'    => $this->profilePayload($fresh),
        ]);
    }

    /**
     * PATCH /api/profile/password
     */
    public function updateMyPassword(Request $request)
    {
        $actor = $this->currentActorContext($request);

        $authId = $request->attributes->get('auth_user_id')
            ?? $request->attributes->get('auth_tokenable_id')
            ?? $this->currentUserId($request);

        if (!$authId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $v = Validator::make($request->all(), [
            'current_password'          => 'required|string',
            'new_password'              => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors(),
            ], 422);
        }

        $data = $v->validated();

        $user = DB::table('users')
            ->where('id', (int) $authId)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || (isset($user->status) && $user->status !== 'active')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!Hash::check((string) $data['current_password'], (string) $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Current password is incorrect',
            ], 422);
        }

        if (Hash::check((string) $data['new_password'], (string) $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'New password must be different from current password',
            ], 422);
        }

        DB::table('users')->where('id', (int) $authId)->update([
            'password'   => Hash::make((string) $data['new_password']),
            'updated_at' => now(),
        ]);

        $this->logActivity(
            activity: 'update_profile_password',
            title: 'Profile password updated',
            description: 'A user changed their own password.',
            performedBy: $actor['id'] ?: (int) $user->id,
            performedByName: $actor['name'] ?? ($user->name ?? null),
            targetId: $user->id,
            targetType: 'user',
            properties: [
                'role'  => $actor['role'] ?? ($user->role ?? null),
                'email' => $user->email ?? null,
            ],
            request: $request,
            module: 'profile'
        );

        $this->notifyAdmins(
            'Profile password updated',
            ($user->name ?? 'A user') . ' changed their password.',
            [
                'action'     => 'profile_password_updated',
                'module'     => 'profile',
                'actor_id'   => $actor['id'] ?: (int) $user->id,
                'actor_role' => $actor['role'] ?? ($user->role ?? null),
                'user'       => [
                    'id'    => (int) $user->id,
                    'uuid'  => (string) ($user->uuid ?? ''),
                    'name'  => (string) ($user->name ?? ''),
                    'email' => (string) ($user->email ?? ''),
                    'role'  => (string) ($user->role ?? ''),
                ],
            ],
            '/profile',
            'profile',
            'high'
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Password updated successfully',
        ]);
    }

    /**
     * GET /api/auth/profile
     */
    public function getProfile(Request $request)
    {
        $authId = $request->attributes->get('auth_user_id')
            ?? $request->attributes->get('auth_tokenable_id')
            ?? $this->currentUserId($request);

        if (!$authId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = DB::table('users')
            ->where('id', (int) $authId)
            ->whereNull('deleted_at')
            ->first();

        if (!$user || (isset($user->status) && $user->status !== 'active')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user'   => $this->profilePayload($user),
        ]);
    }

    /* =========================================================
     |                     Helper methods
     |=========================================================*/

    protected function issueToken(int $userId, ?Carbon $expiresAt = null): string
    {
        $plain = bin2hex(random_bytes(40));

        DB::table('personal_access_tokens')->insert([
            'tokenable_type' => self::USER_TYPE,
            'tokenable_id'   => $userId,
            'name'           => 'user_token',
            'token'          => hash('sha256', $plain),
            'abilities'      => json_encode(['*']),
            'last_used_at'   => null,
            'expires_at'     => $expiresAt ? $expiresAt->toDateTimeString() : null,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $plain;
    }

    protected function extractToken(Request $request): ?string
    {
        $header = $request->header('Authorization');

        if (!$header || !preg_match('/Bearer\s(\S+)/', $header, $m)) {
            return null;
        }

        return $m[1];
    }

    protected function currentUserId(Request $request): ?int
    {
        $plain = $this->extractToken($request);
        if (!$plain) {
            return null;
        }

        $rec = DB::table('personal_access_tokens')
            ->where('token', hash('sha256', $plain))
            ->where('tokenable_type', self::USER_TYPE)
            ->first();

        return $rec ? (int) $rec->tokenable_id : null;
    }

    protected function currentActorContext(Request $request): array
    {
        $actorId = (int) (
            $request->attributes->get('auth_user_id')
            ?? $request->attributes->get('auth_tokenable_id')
            ?? $this->currentUserId($request)
            ?? 0
        );

        if ($actorId <= 0) {
            return ['id' => 0, 'name' => null, 'role' => null];
        }

        $actor = DB::table('users')
            ->select('id', 'name', 'role')
            ->where('id', $actorId)
            ->first();

        return [
            'id'   => $actor ? (int) $actor->id : $actorId,
            'name' => $actor->name ?? null,
            'role' => $actor->role ?? null,
        ];
    }

    protected function manageUsersLink(?int $id = null): string
    {
        return $id ? '/user/manage?user_id=' . $id : '/user/manage';
    }

    protected function profilePayload(object $user): array
    {
        return [
            'id'                       => (int) $user->id,
            'uuid'                     => (string) ($user->uuid ?? ''),
            'name'                     => (string) ($user->name ?? ''),
            'email'                    => (string) ($user->email ?? ''),
            'phone_number'             => (string) ($user->phone_number ?? ''),
            'alternative_email'        => (string) ($user->alternative_email ?? ''),
            'alternative_phone_number' => (string) ($user->alternative_phone_number ?? ''),
            'whatsapp_number'          => (string) ($user->whatsapp_number ?? ''),
            'image'                    => $this->publicImageUrl($user->image ?? null),
            'cv'                       => $this->publicFileUrl($user->cv ?? null),
            'address'                  => (string) ($user->address ?? ''),
            'role'                     => (string) ($user->role ?? ''),
            'role_short_form'          => (string) ($user->role_short_form ?? ''),
            'slug'                     => (string) ($user->slug ?? ''),
            'status'                   => (string) ($user->status ?? ''),
            'last_login_at'            => $user->last_login_at,
            'last_login_ip'            => $user->last_login_ip,
            'created_at'               => $user->created_at,
            'updated_at'               => $user->updated_at,
        ];
    }

    protected function publicUserPayload(object $user): array
    {
        return [
            'id'              => (int) $user->id,
            'uuid'            => (string) ($user->uuid ?? ''),
            'name'            => (string) ($user->name ?? ''),
            'email'           => (string) ($user->email ?? ''),
            'phone_number'    => (string) ($user->phone_number ?? ''),
            'role'            => (string) ($user->role ?? ''),
            'role_short_form' => (string) ($user->role_short_form ?? ''),
            'slug'            => (string) ($user->slug ?? ''),
            'image'           => $this->publicImageUrl($user->image ?? null),
            'status'          => (string) ($user->status ?? ''),
        ];
    }

    protected function normalizeRole(?string $role, ?string $short = null): array
    {
        $key = Str::of((string) $role)
            ->lower()
            ->trim()
            ->replace(['_', '-'], ' ')
            ->replaceMatches('/\s+/', ' ')
            ->toString();

        $map = [
            'admin'             => 'admin',
            'administrator'     => 'admin',
            'adm'               => 'admin',
            'super admin'       => 'admin',
            'superadmin'        => 'admin',

            'hr'                => 'hr',
            'human resources'   => 'hr',
            'human resource'    => 'hr',
            'hr manager'        => 'hr',
            'people ops'        => 'hr',
            'people operations' => 'hr',
            'recruiter'         => 'hr',

            'employee'          => 'employee',
            'staff'             => 'employee',
            'team member'       => 'employee',
            'associate'         => 'employee',
            'executive'         => 'employee',
            'manager'           => 'employee',
            'supervisor'        => 'employee',
            'officer'           => 'employee',
            'member'            => 'employee',
            'emp'               => 'employee',
            'staff member'      => 'employee',
            'user'              => 'employee',
        ];

        $r = $map[$key] ?? $key;

        if (!in_array($r, self::ROLES, true)) {
            $r = 'employee';
        }

        $short = $short ?: (self::ROLE_SHORT[$r] ?? 'EMP');

        return [$r, strtoupper($short)];
    }

    protected function saveProfileImage($uploadedFile)
    {
        if (!$uploadedFile || !$uploadedFile->isValid()) {
            return false;
        }

        $destDir = public_path('assets/media/images/UserProfileImage');
        if (!File::isDirectory($destDir)) {
            File::makeDirectory($destDir, 0755, true);
        }

        $ext      = strtolower($uploadedFile->getClientOriginalExtension() ?: 'bin');
        $filename = 'usr_' . date('Ymd_His') . '_' . Str::lower(Str::random(16)) . '.' . $ext;

        $uploadedFile->move($destDir, $filename);

        return '/assets/media/images/UserProfileImage/' . $filename;
    }

    protected function publicImageUrl(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = $path ?: $value;
        $path = '/' . ltrim($path, '/');

        return url($path);
    }

    protected function deleteManagedProfileImage(?string $url): void
    {
        if (empty($url)) {
            return;
        }

        $path = parse_url($url, PHP_URL_PATH);
        $path = $path ?: $url;
        $path = '/' . ltrim($path, '/');

        if (Str::startsWith($path, '/assets/media/images/UserProfileImage/')) {
            $abs = public_path(ltrim($path, '/'));
            if (File::exists($abs)) {
                @File::delete($abs);
            }
        }
    }

    protected function publicFileUrl(?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        $path = parse_url($value, PHP_URL_PATH);
        $path = $path ?: $value;
        $path = '/' . ltrim($path, '/');

        return url($path);
    }

    protected function deleteManagedCv(?string $pathOrUrl): void
    {
        if (empty($pathOrUrl)) {
            return;
        }

        $path = parse_url($pathOrUrl, PHP_URL_PATH);
        $path = $path ?: $pathOrUrl;
        $path = '/' . ltrim($path, '/');

        if (Str::startsWith($path, '/assets/images/usercv/')) {
            $abs = public_path(ltrim($path, '/'));
            if (File::exists($abs)) {
                @File::delete($abs);
            }
        }
    }
}
