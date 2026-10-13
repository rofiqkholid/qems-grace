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
        // 1. Shift sequence_id for top-level menus (level_menu_id = 2) starting from sequence_id 1 onwards
        // so that Demo can take sequence_id = 1 at top level.
        $topLevelMenus = DB::table('t100_menus')
            ->where('level_menu_id', 2)
            ->where('id', '!=', 127)
            ->get();

        foreach ($topLevelMenus as $menu) {
            DB::table('t100_menus')
                ->where('id', $menu->id)
                ->update(['sequence_id' => $menu->sequence_id + 1]);
        }

        // 2. Insert "Demo" menu (id = 127, sequence_id = 1, group_id = 11, sub_group_id = 127, level_menu_id = 2)
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 127],
            [
                'sequence_id' => 1,
                'level_menu_id' => 2,
                'group_id' => 11,
                'sub_group_id' => 127,
                'menu' => 'introducing',
                'menu_name' => 'Introducing',
                'icon' => '<span></span>'
            ]
        );

        // 3. Ensure all active users have permission (is_view = 1) for Demo menu (id = 127)
        $users = DB::table('users')->select('id')->get();
        foreach ($users as $u) {
            // Check if user has permission to view Dashboard (menu 100) or default to 1
            $dashPerm = DB::table('t100_user_menus_permission')
                ->where('id_user', $u->id)
                ->where('id_menus', 100)
                ->first();

            $isView = $dashPerm ? $dashPerm->is_view : 1;

            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $u->id,
                    'id_menus' => 127
                ],
                [
                    'is_view' => $isView,
                    'is_delete' => 0
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
        // Delete permissions for Demo menu
        DB::table('t100_user_menus_permission')->where('id_menus', 127)->delete();

        // Delete Demo menu
        DB::table('t100_menus')->where('id', 127)->delete();

        // Restore original sequence_id for level_menu_id = 2
        $topLevelMenus = DB::table('t100_menus')
            ->where('level_menu_id', 2)
            ->get();

        foreach ($topLevelMenus as $menu) {
            if ($menu->sequence_id > 1) {
                DB::table('t100_menus')
                    ->where('id', $menu->id)
                    ->update(['sequence_id' => $menu->sequence_id - 1]);
            }
        }
    }
};
