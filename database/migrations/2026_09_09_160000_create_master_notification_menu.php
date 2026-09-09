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
        // Insert "Notification" menu under "Data Master" (group_id = 6, sub_group_id = 95)
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 124],
            [
                'sequence_id' => 11,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/notification',
                'menu_name' => 'Notification',
                'icon' => '<span></span>'
            ]
        );

        // Copy permissions from parent menu (ID 95) to the new menu
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 95)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 124
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
        DB::table('t100_user_menus_permission')->where('id_menus', 124)->delete();
        DB::table('t100_menus')->where('id', 124)->delete();
    }
};
