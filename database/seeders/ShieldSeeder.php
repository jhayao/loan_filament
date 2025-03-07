<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_area","view_any_area","create_area","update_area","restore_area","restore_any_area","replicate_area","reorder_area","delete_area","delete_any_area","force_delete_area","force_delete_any_area","view_branch","view_any_branch","create_branch","update_branch","restore_branch","restore_any_branch","replicate_branch","reorder_branch","delete_branch","delete_any_branch","force_delete_branch","force_delete_any_branch","view_company","view_any_company","create_company","update_company","restore_company","restore_any_company","replicate_company","reorder_company","delete_company","delete_any_company","force_delete_company","force_delete_any_company","view_customer","view_any_customer","create_customer","update_customer","restore_customer","restore_any_customer","replicate_customer","reorder_customer","delete_customer","delete_any_customer","force_delete_customer","force_delete_any_customer","view_email::template","view_any_email::template","create_email::template","update_email::template","restore_email::template","restore_any_email::template","replicate_email::template","reorder_email::template","delete_email::template","delete_any_email::template","force_delete_email::template","force_delete_any_email::template","view_email::template::theme","view_any_email::template::theme","create_email::template::theme","update_email::template::theme","restore_email::template::theme","restore_any_email::template::theme","replicate_email::template::theme","reorder_email::template::theme","delete_email::template::theme","delete_any_email::template::theme","force_delete_email::template::theme","force_delete_any_email::template::theme","view_staff","view_any_staff","create_staff","update_staff","restore_staff","restore_any_staff","replicate_staff","reorder_staff","delete_staff","delete_any_staff","force_delete_staff","force_delete_any_staff","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","page_MyProfilePage","page_Backups","super"]},{"name":"Admin","guard_name":"web","permissions":["view_area","create_area","update_area","restore_area","delete_area","view_branch","create_branch","update_branch","restore_branch","delete_branch","force_delete_any_branch","view_company","create_company","update_company","restore_company","delete_company","view_customer","create_customer","update_customer","restore_customer","restore_any_customer","delete_customer","view_email::template","create_email::template","update_email::template","restore_email::template","delete_email::template","view_email::template::theme","view_any_email::template::theme","create_email::template::theme","update_email::template::theme","restore_email::template::theme","delete_email::template::theme","view_staff","create_staff","update_staff","restore_staff","delete_staff","view_user","create_user","update_user","restore_user","delete_user","page_MyProfilePage","App\\Models\\Company"]},{"name":"Manager","guard_name":"web","permissions":["view_area","create_area","update_area","restore_area","delete_area","view_branch","create_branch","update_branch","delete_branch","view_company","view_customer","create_customer","update_customer","delete_customer","view_email::template","create_email::template","update_email::template","delete_email::template","view_email::template::theme","create_email::template::theme","update_email::template::theme","delete_email::template::theme","view_staff","create_staff","update_staff","delete_staff","view_user","create_user","update_user","delete_user","App\\Models\\Branch"]},{"name":"Secretary","guard_name":"web","permissions":["view_area","create_area","update_area","delete_area","view_branch","view_company","view_customer","create_customer","update_customer","delete_customer","view_email::template","create_email::template","update_email::template","delete_email::template","view_email::template::theme","view_any_email::template::theme","create_email::template::theme","update_email::template::theme","delete_email::template::theme","view_staff","App\\Models\\Branch"]},{"name":"Collector","guard_name":"web","permissions":["view_area","view_branch","view_company","view_customer","view_any_customer","create_customer","update_customer","App\\Models\\Area"]}]';
        $directPermissions = '{"72":{"name":"view_role","guard_name":"web"},"73":{"name":"view_any_role","guard_name":"web"},"74":{"name":"create_role","guard_name":"web"},"75":{"name":"update_role","guard_name":"web"},"76":{"name":"delete_role","guard_name":"web"},"77":{"name":"delete_any_role","guard_name":"web"},"107":{"name":"view_shield::role","guard_name":"web"},"108":{"name":"view_any_shield::role","guard_name":"web"},"109":{"name":"create_shield::role","guard_name":"web"},"110":{"name":"update_shield::role","guard_name":"web"},"111":{"name":"delete_shield::role","guard_name":"web"},"112":{"name":"delete_any_shield::role","guard_name":"web"}}';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
