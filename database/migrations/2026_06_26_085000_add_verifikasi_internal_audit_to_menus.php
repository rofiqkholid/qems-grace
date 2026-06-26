<?php

use Illuminate\Database\Migrations\Migration;
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
        // Insert menu item with ID 112
        DB::table('t100_menus')->insert([
            'id' => 112,
            'sequence_id' => 3,
            'level_menu_id' => 3,
            'group_id' => 9,
            'sub_group_id' => 107,
            'menu' => 'verifikasi-internal-audit',
            'menu_name' => 'Verifikasi Audit',
            'icon' => '<span></span>'
        ]);

        // Copy permissions from menu 108 (Internal Audit Form)
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 108)->get();
        foreach ($permissions as $permission) {
            DB::table('t100_user_menus_permission')->insert([
                'id_user' => $permission->id_user,
                'id_menus' => 112,
                'is_view' => $permission->is_view,
                'is_delete' => $permission->is_delete,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('t100_user_menus_permission')->where('id_menus', 112)->delete();
        DB::table('t100_menus')->where('id', 112)->delete();
    }
};
