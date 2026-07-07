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
        // Insert menu 113
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 113],
            [
                'sequence_id' => 4,
                'level_menu_id' => 3,
                'group_id' => 7,
                'sub_group_id' => 100,
                'menu' => 'dashboard-internal-audit',
                'menu_name' => 'Internal Audit',
                'icon' => '<span></span>'
            ]
        );

        // Copy permissions from menu 101 to menu 113
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 101)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 113
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
        DB::table('t100_user_menus_permission')->where('id_menus', 113)->delete();
        DB::table('t100_menus')->where('id', 113)->delete();
    }
};
