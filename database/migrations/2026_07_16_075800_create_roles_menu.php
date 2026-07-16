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
        // Insert menu 114
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 114],
            [
                'sequence_id' => 7,
                'level_menu_id' => 3,
                'group_id' => 6,
                'sub_group_id' => 95,
                'menu' => 'data-master/roles',
                'menu_name' => 'Roles',
                'icon' => '<span></span>'
            ]
        );

        // Copy permissions from menu 95 (Data Master) to menu 114
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 95)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 114
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
        DB::table('t100_user_menus_permission')->where('id_menus', 114)->delete();
        DB::table('t100_menus')->where('id', 114)->delete();
    }
};
