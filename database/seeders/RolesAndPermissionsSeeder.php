<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions grouped by module
        $modules = [
            'products' => ['view_products', 'create_products', 'edit_products', 'delete_products'],
            'categories' => ['view_categories', 'create_categories', 'edit_categories', 'delete_categories'],
            'sub_categories' => ['view_sub_categories', 'create_sub_categories', 'edit_sub_categories', 'delete_sub_categories'],
            'brands' => ['view_brands', 'create_brands', 'edit_brands', 'delete_brands'],
            'concerns' => ['view_concerns', 'create_concerns', 'edit_concerns', 'delete_concerns'],
            'skin_types' => ['view_skin_types', 'create_skin_types', 'edit_skin_types', 'delete_skin_types'],
            'quiz_questions' => ['view_quiz_questions', 'create_quiz_questions', 'edit_quiz_questions', 'delete_quiz_questions'],
            'loyalty_levels' => ['view_loyalty_levels', 'create_loyalty_levels', 'edit_loyalty_levels', 'delete_loyalty_levels'],
            'offers' => ['view_offers', 'create_offers', 'edit_offers', 'delete_offers'],
            'coupons' => ['view_coupons', 'create_coupons', 'edit_coupons', 'delete_coupons'],
            'orders' => ['view_orders', 'edit_orders', 'delete_orders'],
            'settings' => ['view_settings', 'edit_settings'],
            'users' => ['view_users', 'create_users', 'edit_users', 'delete_users'],
            'assessments' => ['view_assessments', 'delete_assessments'],
            'roles' => ['view_roles', 'create_roles', 'edit_roles', 'delete_roles'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, 'web');
                $allPermissions[] = $permission;
            }
        }

        // 2. Create Roles and sync permissions
        $superAdmin = Role::findOrCreate('super_admin', 'web');
        $superAdmin->syncPermissions($allPermissions);

        $manager = Role::findOrCreate('manager', 'web');
        $managerPermissions = array_diff($allPermissions, ['view_roles', 'create_roles', 'edit_roles', 'delete_roles']);
        $manager->syncPermissions($managerPermissions);

        $editor = Role::findOrCreate('editor', 'web');
        $editorPermissions = [
            'view_products', 'create_products', 'edit_products',
            'view_categories', 'create_categories', 'edit_categories',
            'view_sub_categories', 'create_sub_categories', 'edit_sub_categories',
            'view_brands', 'create_brands', 'edit_brands',
            'view_offers', 'create_offers', 'edit_offers',
            'view_coupons', 'create_coupons', 'edit_coupons',
        ];
        $editor->syncPermissions($editorPermissions);

        // 3. Assign super_admin role to admin users
        $adminUsers = User::where('type', 'admin')->get();
        foreach ($adminUsers as $user) {
            $user->assignRole($superAdmin);
        }
    }
}
