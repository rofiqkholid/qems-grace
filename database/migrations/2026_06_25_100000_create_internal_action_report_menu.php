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
        // Insert menu 110
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 110],
            [
                'sequence_id' => 2,
                'level_menu_id' => 3,
                'group_id' => 9,
                'sub_group_id' => 107,
                'menu' => 'internal-action-report',
                'menu_name' => 'Action Report',
                'icon' => '<span></span>'
            ]
        );

        // Copy permissions from menu 108 to menu 110
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 108)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 110
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
        DB::table('t100_user_menus_permission')->where('id_menus', 110)->delete();
        DB::table('t100_menus')->where('id', 110)->delete();
    }
};
