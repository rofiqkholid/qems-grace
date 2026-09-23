<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert "Station Mech" menu under "Data Master" (group_id = 6, sub_group_id = 95)
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 126],
            [
                'sequence_id' => 5,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/station-mech',
                'menu_name' => 'Station Mech',
                'icon' => '<span></span>'
            ]
        );

        // Re-sequence menus under Data Master so Station Mech is positioned right above Intr Check Item
        $sequenceOrder = [
            96 => 1,  // Line Checked
            97 => 2,  // Category
            98 => 3,  // Departement
            99 => 4,  // Check Item
            126 => 5, // Station Mech
            109 => 6, // Intr Check Item
            111 => 7, // Clauses
            114 => 8, // Roles
            115 => 9, // User Auditor
            121 => 10,// KPI List
            122 => 11,// KPI Unit
            124 => 12,// Notification
        ];

        foreach ($sequenceOrder as $menuId => $seq) {
            DB::table('t100_menus')->where('id', $menuId)->update(['sequence_id' => $seq]);
        }

        // Copy permissions from parent menu (ID 95) to menu 126
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 95)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 126
                ],
                [
                    'is_view' => $perm->is_view,
                    'is_delete' => $perm->is_delete
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('t100_user_menus_permission')->where('id_menus', 126)->delete();
        DB::table('t100_menus')->where('id', 126)->delete();
    }
};
