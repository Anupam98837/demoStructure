<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Concerns\PersistsAdminNotifications;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RolePrivilegeController extends Controller
{
    use PersistsAdminNotifications;

    /* =========================
     * Actor helper (who is doing the action)
     * ========================= */
    private function actor(Request $request): array
    {
        return [
            'role' => $request->attributes->get('auth_role'),
            'type' => $request->attributes->get('auth_tokenable_type'),
            'id'   => (int) ($request->attributes->get('auth_tokenable_id') ?? 0),
        ];
    }

    private function normalizeRoleKey($role): string
    {
        return strtolower(trim((string) $role));
    }

    /** Optional extra safety (routes middleware can also enforce). */
    private function canManageRolePrivileges(array $actor): bool
    {
        $r = $this->normalizeRoleKey($actor['role'] ?? '');
        return $r === 'admin';
    }

    /* =========================
     * ACTIVITY LOGGING (user_data_activity_log)
     * ========================= */
    private function toJsonOrNull($value): ?string
    {
        if ($value === null) return null;

        return json_encode(
            $value,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR
        );
    }

    private function logActivity(
        Request $r,
        string $activity,        // create|update|delete|error|etc
        string $module,          // e.g. role_privileges
        string $tableName,       // e.g. role_privileges
        ?int $recordId = null,
        ?array $changedFields = null,
        $oldValues = null,
        $newValues = null,
        ?string $note = null
    ): void {
        try {
            if (!Schema::hasTable('user_data_activity_log')) return;

            $actor = $this->actor($r);

            DB::table('user_data_activity_log')->insert([
                'performed_by'      => (int) ($actor['id'] ?? 0),
                'performed_by_role' => $actor['role'] ?? null,
                'ip'                => $r->ip(),
                'user_agent'        => substr((string) $r->userAgent(), 0, 512),

                'activity'          => $activity,
                'module'            => $module,

                'table_name'        => $tableName,
                'record_id'         => $recordId,

                'changed_fields'    => $changedFields ? $this->toJsonOrNull(array_values($changedFields)) : null,
                'old_values'        => $this->toJsonOrNull($oldValues),
                'new_values'        => $this->toJsonOrNull($newValues),

                'log_note'          => $note,

                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Never break primary functionality due to logging failure
        }
    }

    /* =========================
     * Utilities
     * ========================= */

    /** Normalize menu href for UI (keeps http(s) absolute; otherwise ensures single leading slash). */
    private function normalizeHrefForResponse($href): string
    {
        $href = (string) ($href ?? '');
        if ($href === '') return '';
        if (preg_match('#^https?://#i', $href)) return $href;
        return '/' . ltrim($href, '/');
    }

    private function resolveRoleFromRequest(array $data): string
    {
        if (!empty($data['role'])) return $this->normalizeRoleKey($data['role']);
        if (!empty($data['role_name'])) return $this->normalizeRoleKey($data['role_name']);
        return '';
    }

    /**
     * Get role_privileges row.
     * - $withTrashed=true  => include soft-deleted rows (needed for revive)
     * - $withTrashed=false => only active row
     */
    private function getRolePrivilegeRow(string $role, bool $withTrashed = true): ?object
    {
        $q = DB::table('role_privileges')->where('role', $role);
        if (!$withTrashed) $q->whereNull('deleted_at');

        return $q->orderByDesc('id')->first();
    }

    /* ============================================================
     * TREE STORAGE HELPERS (same as UserPrivilegeController)
     * ============================================================ */

    private function decodeStoredPrivileges($value): array
    {
        if ($value === null || $value === '') return [];

        if (is_array($value)) $arr = $value;
        else $arr = json_decode((string) $value, true);

        if (!is_array($arr)) return [];

        // old flat format: [1,2,3]
        $isFlat = true;
        foreach ($arr as $v) {
            if (!is_numeric($v)) { $isFlat = false; break; }
        }
        if ($isFlat) {
            $privObjs = array_map(fn($x)=>['id'=>(int)$x,'action'=>null], $arr);
            return [[
                'id' => 0,
                'type' => 'header',
                'children' => [[
                    'id' => 0,
                    'type' => 'page',
                    'privileges' => $privObjs
                ]]
            ]];
        }

        return $arr;
    }

    private function extractPrivilegeIdsFromTree(array $tree): array
    {
        $ids = [];
        $map = [];

        $walk = function ($nodes) use (&$walk, &$ids, &$map) {
            foreach ($nodes as $node) {
                if (isset($node['privileges']) && is_array($node['privileges'])) {
                    foreach ($node['privileges'] as $p) {
                        if (is_array($p) && isset($p['id'])) {
                            $pid = (int) $p['id'];
                            if ($pid > 0) {
                                $ids[] = $pid;
                                if (!empty($p['action'])) $map[$pid] = (string) $p['action'];
                            }
                        } elseif (is_numeric($p)) {
                            $pid = (int) $p;
                            if ($pid > 0) $ids[] = $pid;
                        }
                    }
                }

                if (!empty($node['children']) && is_array($node['children'])) {
                    $walk($node['children']);
                }
            }
        };

        $walk($tree);

        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), fn($x)=>$x>0)));
        sort($ids);

        return ['ids' => $ids, 'map' => $map];
    }

    private function actionMapFromDb(array $privIds): array
    {
        if (empty($privIds)) return [];

        $rows = DB::table('page_privilege')
            ->whereIn('id', $privIds)
            ->whereNull('deleted_at')
            ->select('id', 'action')
            ->get();

        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r->id] = (string) ($r->action ?? '');
        }
        return $map;
    }

    private function normalizeIncomingTree(array $tree): array
    {
        $extracted = $this->extractPrivilegeIdsFromTree($tree);
        $ids = $extracted['ids'];

        $dbActionMap = $this->actionMapFromDb($ids);

        $normalizeNode = function ($node) use (&$normalizeNode, $dbActionMap) {
            $out = [];

            $out['id'] = isset($node['id']) ? (int) $node['id'] : 0;
            if (!empty($node['type'])) $out['type'] = (string) $node['type'];

            if (!empty($node['privileges']) && is_array($node['privileges'])) {
                $privs = [];
                foreach ($node['privileges'] as $p) {
                    if (is_array($p) && isset($p['id'])) {
                        $pid = (int) $p['id'];
                        if ($pid > 0) {
                            $privs[] = [
                                'id'     => $pid,
                                'action' => $dbActionMap[$pid] ?? ($p['action'] ?? null),
                            ];
                        }
                    } elseif (is_numeric($p)) {
                        $pid = (int) $p;
                        if ($pid > 0) {
                            $privs[] = [
                                'id'     => $pid,
                                'action' => $dbActionMap[$pid] ?? null,
                            ];
                        }
                    }
                }

                if (!empty($privs)) {
                    $tmp = [];
                    foreach ($privs as $pp) $tmp[$pp['id']] = $pp;
                    $out['privileges'] = array_values($tmp);
                }
            }

            if (!empty($node['children']) && is_array($node['children'])) {
                $children = [];
                foreach ($node['children'] as $c) $children[] = $normalizeNode($c);
                if (!empty($children)) $out['children'] = $children;
            }

            return $out;
        };

        $clean = [];
        foreach ($tree as $n) $clean[] = $normalizeNode($n);
        return $clean;
    }

    private function buildTreeFromPrivilegeIds(array $privIds): array
    {
        $privIds = array_values(array_unique(array_filter(array_map('intval', $privIds), fn($x)=>$x>0)));
        if (empty($privIds)) return [];

        $rows = DB::table('page_privilege as p')
            ->join('dashboard_menu as m', 'm.id', '=', 'p.dashboard_menu_id')
            ->whereIn('p.id', $privIds)
            ->whereNull('p.deleted_at')
            ->whereNull('m.deleted_at')
            ->select([
                'p.id as priv_id',
                'p.action as priv_action',
                'p.dashboard_menu_id as page_id',
                'm.parent_id',
                'm.is_dropdown_head',
            ])
            ->get();

        $bucket = [];

        foreach ($rows as $r) {
            $pageId = (int) $r->page_id;

            $headerId = 0;
            if (!is_null($r->parent_id)) {
                $headerId = (int) $r->parent_id;
            } elseif ((int)($r->is_dropdown_head ?? 0) === 1) {
                $headerId = $pageId;
            }

            $bucket[$headerId][$pageId][] = [
                'id'     => (int) $r->priv_id,
                'action' => (string) ($r->priv_action ?? null),
            ];
        }

        ksort($bucket);
        $tree = [];

        foreach ($bucket as $headerId => $pages) {
            ksort($pages);
            $children = [];

            foreach ($pages as $pageId => $privs) {
                $tmp = [];
                foreach ($privs as $p) $tmp[(int)$p['id']] = $p;
                $privs = array_values($tmp);

                $children[] = [
                    'id'         => (int) $pageId,
                    'type'       => 'page',
                    'privileges' => $privs,
                ];
            }

            $tree[] = [
                'id'       => (int) $headerId,
                'type'     => 'header',
                'children' => $children,
            ];
        }

        return $tree;
    }

    /**
     * ✅ Upsert (insert or update) role_privileges row.
     * - Revives soft-deleted row
     * - Keeps one row per role (newest row wins)
     */
    private function upsertRolePrivilegesRow(Request $r, string $role, array $tree, array $actor, $now): object
    {
        $rows = DB::table('role_privileges')
            ->where('role', $role)
            ->orderByDesc('id')
            ->get(['id', 'deleted_at']);

        $keep = $rows->first();

        // Soft-delete duplicates (if any exist somehow)
        $dupIds = $rows->skip(1)->pluck('id')->all();
        if (!empty($dupIds)) {
            DB::table('role_privileges')
                ->whereIn('id', $dupIds)
                ->update([
                    'deleted_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        $payloadToStore = json_encode($tree);

        if ($keep) {
            DB::table('role_privileges')
                ->where('id', $keep->id)
                ->update([
                    'role'         => $role, // keep normalized key
                    'privileges'   => $payloadToStore,
                    'assigned_by'  => $actor['id'] ?: null,
                    'created_at_ip'=> $r->ip(),
                    'deleted_at'   => null, // revive if trashed
                    'updated_at'   => $now,
                ]);

            return DB::table('role_privileges')->where('id', $keep->id)->first();
        }

        $id = DB::table('role_privileges')->insertGetId([
            'uuid'         => (string) Str::uuid(),
            'role'         => $role,
            'privileges'   => $payloadToStore,
            'assigned_by'  => $actor['id'] ?: null,
            'created_at_ip'=> $r->ip(),
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        return DB::table('role_privileges')->where('id', $id)->first();
    }

    /* ============================================================
     * SYNC (replace role privileges with given TREE)
     * ============================================================ */
    public function sync(Request $r)
    {
        $data = $r->validate([
            'role' => 'required|string|max:80',

            'tree' => 'required|array|min:0',

            // header
            'tree.*.id' => 'required|integer|min:0',
            'tree.*.type' => 'required|string|in:header',
            'tree.*.children' => 'required|array|min:1',

            // page
            'tree.*.children.*.id' => 'required|integer|min:0',
            'tree.*.children.*.type' => 'required|string|in:page',
            'tree.*.children.*.privileges' => 'required|array|min:0',

            // privileges
            'tree.*.children.*.privileges.*.id' => 'required|integer|exists:page_privilege,id',
            'tree.*.children.*.privileges.*.action' => 'nullable|string',
        ]);

        $actor = $this->actor($r);
        if (!$this->canManageRolePrivileges($actor)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $role = $this->normalizeRoleKey($data['role']);
        if ($role === '') return response()->json(['error' => 'Role is required'], 422);

        $now = now();

        $newTree = $this->normalizeIncomingTree($data['tree']);
        $newIds  = $this->extractPrivilegeIdsFromTree($newTree)['ids'];

        $beforeRow  = $this->getRolePrivilegeRow($role, true);
        $beforeTree = $beforeRow ? $this->decodeStoredPrivileges($beforeRow->privileges ?? null) : [];
        $beforeIds  = $this->extractPrivilegeIdsFromTree($beforeTree)['ids'];

        try {
            $result = DB::transaction(function () use ($r, $role, $newTree, $newIds, $actor, $now) {

                $row = $this->getRolePrivilegeRow($role, true);
                $currentTree = $row ? $this->decodeStoredPrivileges($row->privileges ?? null) : [];
                $curIds = $this->extractPrivilegeIdsFromTree($currentTree)['ids'];

                $addedIds   = array_values(array_diff($newIds, $curIds));
                $removedIds = array_values(array_diff($curIds, $newIds));

                $actionMap = $this->actionMapFromDb(array_values(array_unique(array_merge($newIds, $curIds))));
                $added   = array_map(fn($id)=>['id'=>(int)$id, 'action'=>($actionMap[(int)$id] ?? null)], $addedIds);
                $removed = array_map(fn($id)=>['id'=>(int)$id, 'action'=>($actionMap[(int)$id] ?? null)], $removedIds);

                $savedRow = $this->upsertRolePrivilegesRow($r, $role, $newTree, $actor, $now);

                return [
                    'row' => $savedRow,
                    'added' => $added,
                    'removed' => $removed,
                    'saved_ids' => $newIds,
                    'saved_tree' => $newTree,
                ];
            });

            $this->logActivity(
                $r,
                $beforeRow ? 'update' : 'create',
                'role_privileges',
                'role_privileges',
                isset($result['row']->id) ? (int)$result['row']->id : null,
                ['privileges', 'assigned_by', 'deleted_at'],
                [
                    'target_role' => $role,
                    'before_privilege_ids' => $beforeIds,
                    'before_tree' => $beforeTree,
                ],
                [
                    'target_role' => $role,
                    'after_privilege_ids' => $result['saved_ids'],
                    'after_tree' => $result['saved_tree'],
                    'added' => $result['added'],
                    'removed' => $result['removed'],
                ],
                'Role privileges synced successfully (tree stored).'
            );

            $this->notifyAdmins(
                'Role privileges synced',
                'Privileges were synced for role "' . $role . '".',
                [
                    'action'    => 'sync',
                    'module'    => 'role_privileges',
                    'role'      => $role,
                    'saved_ids' => $result['saved_ids'],
                    'added'     => $result['added'],
                    'removed'   => $result['removed'],
                    'actor'     => $actor,
                ],
                '/role-privileges/manage',
                'role_privileges'
            );

            return response()->json([
                'message' => 'Role privileges synced successfully (tree stored).',
                'role'    => $role,
                'role_privileges_uuid' => $result['row']->uuid ?? null,
                'added'   => $result['added'],
                'removed' => $result['removed'],
                'saved_count' => count($result['saved_ids']),
                'saved_ids'   => $result['saved_ids'],
                'tree'        => $result['saved_tree'],
            ], 200);

        } catch (\Throwable $e) {
            $this->logActivity(
                $r,
                'error',
                'role_privileges',
                'role_privileges',
                $beforeRow ? (int)($beforeRow->id ?? 0) : null,
                ['privileges'],
                [
                    'target_role' => $role,
                    'before_privilege_ids' => $beforeIds,
                ],
                [
                    'target_role' => $role,
                    'attempted_privilege_ids' => $newIds,
                ],
                'Could not sync role privileges: '.$e->getMessage()
            );

            return response()->json(['error' => 'Could not sync role privileges', 'detail' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
     * ASSIGN (merge privilege ids into role)
     * ============================================================ */
    public function assign(Request $r)
    {
        $data = $r->validate([
            'role' => 'required|string|max:80',

            'privilege_id'     => 'sometimes|integer|exists:page_privilege,id',
            'privilege_ids'    => 'sometimes|array|min:1',
            'privilege_ids.*'  => 'integer|exists:page_privilege,id',

            'tree'             => 'sometimes|array',
            'tree.*.id'        => 'required_with:tree|integer',
        ]);

        $actor = $this->actor($r);
        if (!$this->canManageRolePrivileges($actor)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $role = $this->normalizeRoleKey($data['role']);
        if ($role === '') return response()->json(['error' => 'Role is required'], 422);

        $now = now();

        $incomingIds = [];

        if (!empty($data['privilege_id'])) $incomingIds[] = (int) $data['privilege_id'];
        if (!empty($data['privilege_ids']) && is_array($data['privilege_ids'])) {
            foreach ($data['privilege_ids'] as $pid) $incomingIds[] = (int) $pid;
        }

        if (!empty($data['tree']) && is_array($data['tree'])) {
            $normTree = $this->normalizeIncomingTree($data['tree']);
            $incomingIds = array_merge($incomingIds, $this->extractPrivilegeIdsFromTree($normTree)['ids']);
        }

        $incomingIds = array_values(array_unique(array_filter(array_map('intval', $incomingIds), fn($x)=>$x>0)));
        if (empty($incomingIds)) return response()->json(['message' => 'No privileges found in payload.'], 422);

        $beforeRow  = $this->getRolePrivilegeRow($role, true);
        $beforeTree = $beforeRow ? $this->decodeStoredPrivileges($beforeRow->privileges ?? null) : [];
        $beforeIds  = $this->extractPrivilegeIdsFromTree($beforeTree)['ids'];

        try {
            $result = DB::transaction(function () use ($r, $role, $incomingIds, $actor, $now) {

                $row = $this->getRolePrivilegeRow($role, true);
                $currentTree = $row ? $this->decodeStoredPrivileges($row->privileges ?? null) : [];
                $curIds = $this->extractPrivilegeIdsFromTree($currentTree)['ids'];

                $mergedIds = array_values(array_unique(array_merge($curIds, $incomingIds)));
                sort($mergedIds);

                $finalTree = $this->buildTreeFromPrivilegeIds($mergedIds);

                $savedRow = $this->upsertRolePrivilegesRow($r, $role, $finalTree, $actor, $now);

                $actionMap = $this->actionMapFromDb($mergedIds);
                $addedIds = array_values(array_diff($mergedIds, $curIds));
                $added = array_map(fn($id)=>['id'=>(int)$id, 'action'=>($actionMap[(int)$id] ?? null)], $addedIds);

                return [
                    'row'  => $savedRow,
                    'added'=> $added,
                    'tree' => $finalTree,
                    'ids'  => $mergedIds,
                ];
            });

            $this->logActivity(
                $r,
                $beforeRow ? 'update' : 'create',
                'role_privileges',
                'role_privileges',
                isset($result['row']->id) ? (int)$result['row']->id : null,
                ['privileges', 'assigned_by', 'deleted_at'],
                [
                    'target_role' => $role,
                    'before_privilege_ids' => $beforeIds,
                    'before_tree' => $beforeTree,
                    'incoming_privilege_ids' => $incomingIds,
                ],
                [
                    'target_role' => $role,
                    'after_privilege_ids' => $result['ids'],
                    'after_tree' => $result['tree'],
                    'added' => $result['added'],
                ],
                'Role privilege(s) assigned (tree stored).'
            );

            $this->notifyAdmins(
                'Role privileges assigned',
                'Privilege assignment was updated for role "' . $role . '".',
                [
                    'action'    => 'assign',
                    'module'    => 'role_privileges',
                    'role'      => $role,
                    'saved_ids' => $result['ids'],
                    'added'     => $result['added'],
                    'actor'     => $actor,
                ],
                '/role-privileges/manage',
                'role_privileges'
            );

            return response()->json([
                'message' => 'Role privilege(s) assigned (tree stored).',
                'role'    => $role,
                'role_privileges_uuid' => $result['row']->uuid ?? null,
                'added'   => $result['added'],
                'saved_count' => count($result['ids']),
                'saved_ids'   => $result['ids'],
                'tree'        => $result['tree'],
            ], 201);

        } catch (\Throwable $e) {
            $this->logActivity(
                $r,
                'error',
                'role_privileges',
                'role_privileges',
                $beforeRow ? (int)($beforeRow->id ?? 0) : null,
                ['privileges'],
                [
                    'target_role' => $role,
                    'before_privilege_ids' => $beforeIds,
                ],
                [
                    'target_role' => $role,
                    'incoming_privilege_ids' => $incomingIds,
                ],
                'Could not assign role privilege: '.$e->getMessage()
            );

            return response()->json(['message' => 'Could not assign role privilege', 'detail' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
     * UNASSIGN (remove one page_privilege id from role tree)
     * ============================================================ */
    public function unassign(Request $r)
    {
        $data = $r->validate([
            'role'         => 'required|string|max:80',
            'privilege_id' => 'required|integer|exists:page_privilege,id',
        ]);

        $actor = $this->actor($r);
        if (!$this->canManageRolePrivileges($actor)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $role = $this->normalizeRoleKey($data['role']);
        if ($role === '') return response()->json(['error' => 'Role is required'], 422);

        $privId = (int) $data['privilege_id'];
        $now = now();

        $beforeRowActive = $this->getRolePrivilegeRow($role, false);
        $beforeTree = $beforeRowActive ? $this->decodeStoredPrivileges($beforeRowActive->privileges ?? null) : [];
        $beforeIds  = $this->extractPrivilegeIdsFromTree($beforeTree)['ids'];

        try {
            $tx = DB::transaction(function () use ($r, $role, $privId, $actor, $now) {

                $row = $this->getRolePrivilegeRow($role, false);
                if (!$row) {
                    return [
                        'affected' => 0,
                        'row_id' => null,
                        'before_ids' => [],
                        'after_ids' => [],
                        'before_tree' => [],
                        'after_tree' => [],
                    ];
                }

                $tree = $this->decodeStoredPrivileges($row->privileges ?? null);

                $removeFromNodes = function (&$nodes) use (&$removeFromNodes, $privId) {
                    foreach ($nodes as &$node) {
                        if (!empty($node['privileges']) && is_array($node['privileges'])) {
                            $node['privileges'] = array_values(array_filter($node['privileges'], function ($p) use ($privId) {
                                if (is_array($p) && isset($p['id'])) return (int) $p['id'] !== $privId;
                                if (is_numeric($p)) return (int) $p !== $privId;
                                return true;
                            }));
                        }
                        if (!empty($node['children']) && is_array($node['children'])) {
                            $removeFromNodes($node['children']);
                        }
                    }
                };

                $beforeIds = $this->extractPrivilegeIdsFromTree($tree)['ids'];
                $beforeTree = $tree;

                $removeFromNodes($tree);

                $afterIds = $this->extractPrivilegeIdsFromTree($tree)['ids'];
                if (count($afterIds) === count($beforeIds)) {
                    return [
                        'affected' => 0,
                        'row_id' => (int)$row->id,
                        'before_ids' => $beforeIds,
                        'after_ids' => $beforeIds,
                        'before_tree' => $beforeTree,
                        'after_tree' => $beforeTree,
                    ];
                }

                $finalTree = $this->normalizeIncomingTree($tree);

                DB::table('role_privileges')
                    ->where('id', $row->id)
                    ->update([
                        'privileges'    => json_encode($finalTree),
                        'assigned_by'   => $actor['id'] ?: null,
                        'created_at_ip' => $r->ip(),
                        'updated_at'    => $now,
                    ]);

                return [
                    'affected' => 1,
                    'row_id' => (int)$row->id,
                    'before_ids' => $beforeIds,
                    'after_ids' => $afterIds,
                    'before_tree' => $beforeTree,
                    'after_tree' => $finalTree,
                ];
            });

            $affected = (int) ($tx['affected'] ?? 0);

            $this->logActivity(
                $r,
                'update',
                'role_privileges',
                'role_privileges',
                !empty($tx['row_id']) ? (int)$tx['row_id'] : ($beforeRowActive ? (int)($beforeRowActive->id ?? 0) : null),
                $affected ? ['privileges', 'assigned_by'] : [],
                [
                    'target_role' => $role,
                    'requested_privilege_id' => (int)$privId,
                    'before_privilege_ids' => $beforeIds,
                    'before_tree' => $beforeTree,
                ],
                [
                    'target_role' => $role,
                    'requested_privilege_id' => (int)$privId,
                    'after_privilege_ids' => $tx['after_ids'] ?? [],
                    'after_tree' => $tx['after_tree'] ?? [],
                ],
                $affected ? 'Role privilege unassigned.' : 'Privilege not found for this role (no changes).'
            );

            if ($affected) {
                $this->notifyAdmins(
                    'Role privilege unassigned',
                    'A privilege was removed from role "' . $role . '".',
                    [
                        'action'               => 'unassign',
                        'module'               => 'role_privileges',
                        'role'                 => $role,
                        'requested_privilege_id' => (int) $privId,
                        'after_privilege_ids'  => $tx['after_ids'] ?? [],
                        'actor'                => $actor,
                    ],
                    '/role-privileges/manage',
                    'role_privileges'
                );
            }

            return $affected
                ? response()->json(['message' => 'Role privilege unassigned.', 'role' => $role])
                : response()->json(['message' => 'Privilege not found for this role.'], 404);

        } catch (\Throwable $e) {
            $this->logActivity(
                $r,
                'error',
                'role_privileges',
                'role_privileges',
                $beforeRowActive ? (int)($beforeRowActive->id ?? 0) : null,
                ['privileges'],
                [
                    'target_role' => $role,
                    'requested_privilege_id' => (int)$privId,
                    'before_privilege_ids' => $beforeIds,
                ],
                null,
                'Could not unassign role privilege: '.$e->getMessage()
            );

            return response()->json(['message' => 'Could not unassign role privilege', 'detail' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
     * DESTROY (soft-delete whole role row)
     * ============================================================ */
    public function destroy(Request $r)
    {
        $data = $r->validate([
            'uuid' => 'sometimes|uuid|exists:role_privileges,uuid',
            'role' => 'sometimes|string|max:80',
        ]);

        $actor = $this->actor($r);
        if (!$this->canManageRolePrivileges($actor)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $now = now();

        try {
            if (!empty($data['uuid'])) {
                $row = DB::table('role_privileges')->where('uuid', $data['uuid'])->first();
                if (!$row) {
                    $this->logActivity($r, 'delete', 'role_privileges', 'role_privileges', null, [], null, ['uuid' => $data['uuid']], 'Not found');
                    return response()->json(['message' => 'Not found'], 404);
                }

                $affected = DB::table('role_privileges')
                    ->where('uuid', $data['uuid'])
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => $now, 'updated_at' => $now]);

                if (!$affected) {
                    return response()->json(['message' => 'Role privilege record not found (or already deleted).'], 404);
                }

                $this->logActivity(
                    $r, 'delete', 'role_privileges', 'role_privileges',
                    (int)$row->id, ['deleted_at'],
                    ['target_role' => (string)$row->role, 'row_uuid' => (string)$row->uuid, 'deleted_at' => $row->deleted_at],
                    ['target_role' => (string)$row->role, 'row_uuid' => (string)$row->uuid, 'deleted_at' => $now->toDateTimeString()],
                    'Role privileges removed successfully.'
                );

                $this->notifyAdmins(
                    'Role privileges removed',
                    'Stored privileges for role "' . (string) $row->role . '" were removed.',
                    [
                        'action'   => 'delete',
                        'module'   => 'role_privileges',
                        'role'     => (string) $row->role,
                        'row_uuid' => (string) $row->uuid,
                        'actor'    => $actor,
                    ],
                    '/role-privileges/manage',
                    'role_privileges',
                    'high'
                );

                return response()->json(['message' => 'Role privileges removed successfully.', 'role' => (string)$row->role]);
            }

            $role = $this->normalizeRoleKey($data['role'] ?? '');
            if ($role === '') return response()->json(['message' => 'role (or uuid) is required'], 422);

            $row = DB::table('role_privileges')->where('role', $role)->first();
            if (!$row) return response()->json(['message' => 'Not found'], 404);

            $affected = DB::table('role_privileges')
                ->where('role', $role)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now, 'updated_at' => $now]);

            if (!$affected) return response()->json(['message' => 'Role privilege record not found (or already deleted).'], 404);

            $this->logActivity(
                $r, 'delete', 'role_privileges', 'role_privileges',
                (int)$row->id, ['deleted_at'],
                ['target_role' => $role, 'row_uuid' => (string)($row->uuid ?? null), 'deleted_at' => $row->deleted_at],
                ['target_role' => $role, 'row_uuid' => (string)($row->uuid ?? null), 'deleted_at' => $now->toDateTimeString()],
                'Role privileges removed successfully.'
            );

            $this->notifyAdmins(
                'Role privileges removed',
                'Stored privileges for role "' . $role . '" were removed.',
                [
                    'action'   => 'delete',
                    'module'   => 'role_privileges',
                    'role'     => $role,
                    'row_uuid' => (string) ($row->uuid ?? ''),
                    'actor'    => $actor,
                ],
                '/role-privileges/manage',
                'role_privileges',
                'high'
            );

            return response()->json(['message' => 'Role privileges removed successfully.', 'role' => $role]);

        } catch (\Throwable $e) {
            $this->logActivity(
                $r, 'error', 'role_privileges', 'role_privileges',
                null, ['deleted_at'],
                ['uuid' => $data['uuid'] ?? null, 'role' => $data['role'] ?? null],
                null,
                'Could not remove role privileges: '.$e->getMessage()
            );

            return response()->json(['message' => 'Could not remove role privileges', 'detail' => $e->getMessage()], 500);
        }
    }

    /* ============================================================
     * LIST (return stored tree + flat IDs + privilege rows)
     * ============================================================ */
    public function list(Request $r)
    {
        $data = $r->validate([
            'role' => 'required|string|max:80',
        ]);

        $role = $this->normalizeRoleKey($data['role']);
        if ($role === '') return response()->json(['message' => 'role is required'], 422);

        $row = DB::table('role_privileges')
            ->where('role', $role)
            ->whereNull('deleted_at')
            ->first();

        $tree = $row ? $this->decodeStoredPrivileges($row->privileges ?? null) : [];
        $flatIds = $this->extractPrivilegeIdsFromTree($tree)['ids'];

        $privs = [];
        if (!empty($flatIds)) {
            $privs = DB::table('page_privilege')
                ->whereIn('id', $flatIds)
                ->whereNull('deleted_at')
                ->select([
                    'id',
                    'uuid',
                    DB::raw('action as name'),
                    'action',
                    'description',
                    'dashboard_menu_id',
                    'created_at',
                ])
                ->orderBy('action', 'asc')
                ->get();
        }

        return response()->json([
            'role'                => $role,
            'role_privileges_uuid'=> $row->uuid ?? null,
            'tree'                => $tree,
            'flat_privilege_ids'  => $flatIds,
            'data'                => $privs,
        ]);
    }

    /* ============================================================
     * OPTIONAL: Sidebar tree for a ROLE (same output as mySidebarMenus)
     * GET /api/role/sidebar-menus?role=faculty&with_actions=1
     * ============================================================ */
    public function sidebarMenusForRole(Request $r)
    {
        $r->validate([
            'role' => 'required|string|max:80',
            'with_actions' => 'sometimes|boolean',
            'status' => 'sometimes|string', // passthrough (if you want to filter on dashboard_menu.status later)
        ]);

        $role = $this->normalizeRoleKey($r->query('role'));
        if ($role === '') return response()->json(['message' => 'role is required'], 422);

        $withActions = filter_var($r->query('with_actions', false), FILTER_VALIDATE_BOOLEAN);

        $row = DB::table('role_privileges')
            ->where('role', $role)
            ->whereNull('deleted_at')
            ->first();

        $storedTree = $row ? $this->decodeStoredPrivileges($row->privileges ?? null) : [];

        if (empty($storedTree) || !is_array($storedTree)) {
            return response()->json(['role' => $role, 'tree' => []], 200);
        }

        $menuIds = [];
        foreach ($storedTree as $h) {
            if (is_array($h) && !empty($h['id'])) $menuIds[] = (int)$h['id'];
            if (!empty($h['children']) && is_array($h['children'])) {
                foreach ($h['children'] as $p) {
                    if (is_array($p) && !empty($p['id'])) $menuIds[] = (int)$p['id'];
                }
            }
        }
        $menuIds = array_values(array_unique(array_filter($menuIds, fn($x)=>$x>0)));

        $menuCols = ['id','uuid','name','description','created_at','updated_at'];
        if (Schema::hasColumn('dashboard_menu', 'href')) $menuCols[] = 'href';
        if (Schema::hasColumn('dashboard_menu', 'icon_class')) $menuCols[] = 'icon_class';
        if (Schema::hasColumn('dashboard_menu', 'status')) $menuCols[] = 'status';
        if (Schema::hasColumn('dashboard_menu', 'parent_id')) $menuCols[] = 'parent_id';
        if (Schema::hasColumn('dashboard_menu', 'is_dropdown_head')) $menuCols[] = 'is_dropdown_head';
        if (Schema::hasColumn('dashboard_menu', 'order_no')) $menuCols[] = 'order_no';

        $menus = DB::table('dashboard_menu')
            ->whereIn('id', $menuIds)
            ->whereNull('deleted_at')
            ->get($menuCols);

        $menuById = [];
        foreach ($menus as $m) $menuById[(int)$m->id] = $m;

        $actionsByPrivId = [];
        if ($withActions) {
            $allPrivIds = $this->extractPrivilegeIdsFromTree($storedTree)['ids'];
            if (!empty($allPrivIds)) {
                $rows = DB::table('page_privilege')
                    ->whereIn('id', $allPrivIds)
                    ->whereNull('deleted_at')
                    ->get(['id','action']);
                foreach ($rows as $pr) {
                    $actionsByPrivId[(int)$pr->id] = strtolower(trim((string)($pr->action ?? '')));
                }
            }
        }

        $outTree = [];

        foreach ($storedTree as $headerNode) {
            if (!is_array($headerNode)) continue;

            $hid = (int)($headerNode['id'] ?? 0);
            if ($hid <= 0) continue;

            $hm = $menuById[$hid] ?? null;

            $hOut = [
                'id'   => $hid,
                'type' => 'header',
                'name' => $hm->name ?? ($headerNode['name'] ?? null),
            ];

            if ($hm && isset($hm->href)) $hOut['href'] = $this->normalizeHrefForResponse($hm->href);
            if ($hm && property_exists($hm, 'icon_class')) $hOut['icon_class'] = $hm->icon_class ?? null;
            if ($hm && property_exists($hm, 'status')) $hOut['status'] = $hm->status ?? null;
            if ($hm && property_exists($hm, 'is_dropdown_head')) $hOut['is_dropdown_head'] = (bool)($hm->is_dropdown_head ?? false);

            $hOut['children'] = [];

            $children = $headerNode['children'] ?? [];
            if (is_array($children)) {
                foreach ($children as $pageNode) {
                    if (!is_array($pageNode)) continue;

                    $pid = (int)($pageNode['id'] ?? 0);
                    if ($pid <= 0) continue;

                    $pm = $menuById[$pid] ?? null;

                    $pagePrivIds = [];
                    if (!empty($pageNode['privileges']) && is_array($pageNode['privileges'])) {
                        foreach ($pageNode['privileges'] as $pp) {
                            $ppid = is_array($pp) ? (int)($pp['id'] ?? 0) : (is_numeric($pp) ? (int)$pp : 0);
                            if ($ppid > 0) $pagePrivIds[] = $ppid;
                        }
                    }
                    $pagePrivIds = array_values(array_unique($pagePrivIds));

                    if (empty($pagePrivIds)) continue;

                    $pOut = [
                        'id'            => $pid,
                        'type'          => 'page',
                        'name'          => $pm->name ?? ($pageNode['name'] ?? null),
                        'privilege_ids' => $pagePrivIds,
                    ];

                    if ($pm && isset($pm->href)) $pOut['href'] = $this->normalizeHrefForResponse($pm->href);
                    if ($pm && property_exists($pm, 'icon_class')) $pOut['icon_class'] = $pm->icon_class ?? null;
                    if ($pm && property_exists($pm, 'status')) $pOut['status'] = $pm->status ?? null;
                    if ($pm && property_exists($pm, 'parent_id')) $pOut['parent_id'] = $pm->parent_id ? (int)$pm->parent_id : null;

                    if ($withActions) {
                        $acts = [];
                        foreach ($pagePrivIds as $ppid) {
                            $a = $actionsByPrivId[(int)$ppid] ?? '';
                            if ($a !== '') $acts[] = $a;
                        }
                        $acts = array_values(array_unique($acts));
                        sort($acts);
                        $pOut['actions'] = $acts;
                    }

                    $hOut['children'][] = $pOut;
                }
            }

            if (!empty($hOut['children'])) $outTree[] = $hOut;
        }

        return response()->json([
            'role' => $role,
            'tree' => $outTree,
        ], 200);
    }
}
