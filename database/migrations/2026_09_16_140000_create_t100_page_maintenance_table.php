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
        // 1. Create t100_page_maintenance table
        if (!Schema::hasTable('t100_page_maintenance')) {
            Schema::create('t100_page_maintenance', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('menu_id')->unique();
                $table->boolean('is_maintenance')->default(0);
                $table->string('updated_by')->nullable();
                $table->timestamps();
            });
        }

        // 2. Insert "Page Maintenance" menu under "Setting" (group_id = 8, sub_group_id = 104)
        DB::table('t100_menus')->updateOrInsert(
            ['id' => 125],
            [
                'sequence_id' => 3,
                'level_menu_id' => 3,
                'group_id' => 8,
                'sub_group_id' => 104,
                'menu' => 'page-maintenance',
                'menu_name' => 'Page Maintenance',
                'icon' => '<span></span>'
            ]
        );

        // 3. Copy permissions from parent menu (ID 104 - Setting) for Page Maintenance
        $permissions = DB::table('t100_user_menus_permission')->where('id_menus', 104)->get();
        foreach ($permissions as $perm) {
            DB::table('t100_user_menus_permission')->updateOrInsert(
                [
                    'id_user' => $perm->id_user,
                    'id_menus' => 125
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
        DB::table('t100_user_menus_permission')->where('id_menus', 125)->delete();
        DB::table('t100_menus')->where('id', 125)->delete();
        Schema::dropIfExists('t100_page_maintenance');
    }
};
