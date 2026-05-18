<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttendanceCoreSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUsers();
        $menus = $this->seedMenus();
        $privileges = $this->seedPrivileges($menus);
        $this->seedHrRolePrivileges($menus, $privileges);
    }

    private function seedUsers(): void
    {
        $this->upsertUser(
            email: 'hr@gmail.com',
            name: 'HR Manager',
            role: 'hr',
            roleShort: 'HR',
            password: 'hr@12345'
        );

        $this->upsertUser(
            email: 'employee@gmail.com',
            name: 'Employee',
            role: 'employee',
            roleShort: 'EMP',
            password: 'employee@123'
        );
    }

    private function upsertUser(string $email, string $name, string $role, string $roleShort, string $password): void
    {
        $payload = [
            'name' => $name,
            'slug' => Str::slug($name),
            'phone_number' => DB::table('users')->where('email', $email)->value('phone_number') ?: null,
            'email_verified_at' => now(),
            'password' => Hash::make($password),
            'address' => ucfirst($role) . ' account',
            'role' => $role,
            'role_short_form' => $roleShort,
            'status' => 'active',
            'remember_token' => Str::random(10),
            'created_at_ip' => '127.0.0.1',
            'metadata' => json_encode([
                'seeded' => true,
                'module' => 'attendance_core',
            ]),
            'updated_at' => now(),
        ];

        $existing = DB::table('users')->where('email', $email)->first();

        if ($existing) {
            DB::table('users')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('users')->insert($payload + [
            'uuid' => (string) Str::uuid(),
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(8)),
            'email' => $email,
            'created_at' => now(),
        ]);
    }

    private function seedMenus(): array
    {
        return [
            'workforce' => $this->upsertMenu('Workforce', null, null, 'fa-solid fa-users-gear', true, 1),
            'access_control' => $this->upsertMenu('Access Control', null, null, 'fa-solid fa-shield-halved', true, 2),
            'manage_users' => $this->upsertMenu('Manage Users', 'user/manage', 'Create and manage employee accounts.', 'fa-solid fa-user-group', false, 1, 'workforce'),
            'dashboard_menu_manage' => $this->upsertMenu('Manage Dashboard Menu', 'dashboard-menu/manage', 'Maintain sidebar modules.', 'fa-solid fa-puzzle-piece', false, 1, 'access_control'),
            'dashboard_menu_create' => $this->upsertMenu('Create Dashboard Menu', 'dashboard-menu/create', 'Add a new sidebar module.', 'fa-solid fa-plus', false, 2, 'access_control'),
            'page_privilege_manage' => $this->upsertMenu('Manage Page Privileges', 'page-privilege/manage', 'Review page-level actions.', 'fa-solid fa-lock', false, 3, 'access_control'),
            'page_privilege_create' => $this->upsertMenu('Create Page Privilege', 'page-privilege/create', 'Create a new page permission.', 'fa-solid fa-key', false, 4, 'access_control'),
            'role_privilege_manage' => $this->upsertMenu('Assign Role Privileges', 'role-privileges/manage', 'Map actions to roles.', 'fa-solid fa-user-shield', false, 5, 'access_control'),
            'user_privilege_manage' => $this->upsertMenu('Assign User Privileges', 'user-privileges/manage', 'Grant user-specific access.', 'fa-solid fa-user-lock', false, 6, 'access_control'),
        ];
    }

    private function upsertMenu(
        string $name,
        ?string $href,
        ?string $description,
        string $icon,
        bool $isHeader,
        int $position,
        ?string $parentKey = null
    ): array {
        static $cache = [];

        $parentId = $parentKey && isset($cache[$parentKey]) ? $cache[$parentKey]['id'] : null;
        $existing = DB::table('dashboard_menu')->where('name', $name)->first();

        $payload = [
            'parent_id' => $parentId,
            'position' => $position,
            'icon_class' => $icon,
            'href' => $href,
            'description' => $description,
            'is_dropdown_head' => $isHeader ? 1 : 0,
            'status' => 'Active',
            'deleted_at' => null,
            'updated_at' => now(),
            'updated_at_ip' => '127.0.0.1',
        ];

        if ($existing) {
            DB::table('dashboard_menu')->where('id', $existing->id)->update($payload);
            $record = (array) DB::table('dashboard_menu')->where('id', $existing->id)->first();
        } else {
            $id = DB::table('dashboard_menu')->insertGetId($payload + [
                'uuid' => (string) Str::uuid(),
                'name' => $name,
                'created_at' => now(),
                'created_at_ip' => '127.0.0.1',
            ]);
            $record = (array) DB::table('dashboard_menu')->where('id', $id)->first();
        }

        $cacheKey = Str::snake($name);
        $cache[$cacheKey] = $record;

        return $record;
    }

    private function seedPrivileges(array $menus): array
    {
        return [
            'manage_users_view' => $this->upsertPrivilege((int) $menus['manage_users']['id'], 'view', 'View user records and lists.'),
            'manage_users_create' => $this->upsertPrivilege((int) $menus['manage_users']['id'], 'create', 'Create employee records.'),
            'manage_users_edit' => $this->upsertPrivilege((int) $menus['manage_users']['id'], 'edit', 'Edit employee records.'),
            'manage_users_delete' => $this->upsertPrivilege((int) $menus['manage_users']['id'], 'delete', 'Delete employee records.'),
            'dashboard_menu_view' => $this->upsertPrivilege((int) $menus['dashboard_menu_manage']['id'], 'view', 'View dashboard menus.'),
            'page_privilege_view' => $this->upsertPrivilege((int) $menus['page_privilege_manage']['id'], 'view', 'View page privileges.'),
            'role_privilege_view' => $this->upsertPrivilege((int) $menus['role_privilege_manage']['id'], 'view', 'View role privileges.'),
            'user_privilege_view' => $this->upsertPrivilege((int) $menus['user_privilege_manage']['id'], 'view', 'View user privileges.'),
        ];
    }

    private function upsertPrivilege(int $menuId, string $action, string $description): array
    {
        $key = strtolower(trim(($menuId . '.' . $action)));
        $existing = DB::table('page_privilege')
            ->where('dashboard_menu_id', $menuId)
            ->where('action', $action)
            ->first();

        $payload = [
            'key' => $key,
            'description' => $description,
            'status' => 'Active',
            'deleted_at' => null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('page_privilege')->where('id', $existing->id)->update($payload);
            return (array) DB::table('page_privilege')->where('id', $existing->id)->first();
        }

        $id = DB::table('page_privilege')->insertGetId($payload + [
            'uuid' => (string) Str::uuid(),
            'dashboard_menu_id' => $menuId,
            'action' => $action,
            'created_at' => now(),
            'created_at_ip' => '127.0.0.1',
        ]);

        return (array) DB::table('page_privilege')->where('id', $id)->first();
    }

    private function seedHrRolePrivileges(array $menus, array $privileges): void
    {
        $tree = [[
            'id' => (int) $menus['workforce']['id'],
            'type' => 'header',
            'children' => [[
                'id' => (int) $menus['manage_users']['id'],
                'type' => 'page',
                'privileges' => [
                    ['id' => (int) $privileges['manage_users_view']['id'], 'action' => 'view'],
                    ['id' => (int) $privileges['manage_users_create']['id'], 'action' => 'create'],
                    ['id' => (int) $privileges['manage_users_edit']['id'], 'action' => 'edit'],
                    ['id' => (int) $privileges['manage_users_delete']['id'], 'action' => 'delete'],
                ],
            ]],
        ]];

        $existing = DB::table('role_privileges')->where('role', 'hr')->first();
        $payload = [
            'privileges' => json_encode($tree, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at_ip' => '127.0.0.1',
            'updated_at' => now(),
            'deleted_at' => null,
        ];

        if ($existing) {
            DB::table('role_privileges')->where('id', $existing->id)->update($payload);
            return;
        }

        DB::table('role_privileges')->insert($payload + [
            'uuid' => (string) Str::uuid(),
            'role' => 'hr',
            'assigned_by' => DB::table('users')->where('email', 'admin@gmail.com')->value('id'),
            'created_at' => now(),
        ]);
    }
}
