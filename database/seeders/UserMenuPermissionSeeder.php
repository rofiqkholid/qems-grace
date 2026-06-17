<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UserMenuPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Target users with Data Master access (view only or full delete)
        $adminNiksWithDelete = ['270723-001', '260422-001', '121020-002'];
        $adminNiksViewOnly = ['031114-001'];
        
        $menuIds = [90, 91, 95, 96, 97, 98, 99, 103, 104, 105]; // Genba Form, Findings, Data Master and its submenus, User Management, Setting, Menu Management

        // Seed users with Delete permission
        $deleteUsers = User::whereIn('username', $adminNiksWithDelete)->get();
        foreach ($deleteUsers as $user) {
            foreach ($menuIds as $menuId) {
                DB::table('t100_user_menus_permission')->updateOrInsert(
                    [
                        'id_user' => $user->id,
                        'id_menus' => $menuId,
                    ],
                    [
                        'is_view' => 1,
                        'is_delete' => 1
                    ]
                );
            }
        }

        // Seed users with View Only permission
        $viewUsers = User::whereIn('username', $adminNiksViewOnly)->get();
        foreach ($viewUsers as $user) {
            foreach ($menuIds as $menuId) {
                DB::table('t100_user_menus_permission')->updateOrInsert(
                    [
                        'id_user' => $user->id,
                        'id_menus' => $menuId,
                    ],
                    [
                        'is_view' => 1,
                        'is_delete' => 0
                    ]
                );
            }
        }

        // Give full access (all views and deletes) to User ID 53
        $allMenuIds = DB::table('t100_menus')->pluck('id')->toArray();
        foreach ($allMenuIds as $menuId) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => 53,
                    'id_menus' => $menuId,
                ],
                [
                    'is_view' => 1,
                    'is_delete' => 1
                ]
            );
        }
    }
}
