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
        // Update main menu 107
        DB::table('t100_menus')
            ->where('id', 107)
            ->update([
                'menu' => 'internal-audit-main',
                'menu_name' => 'Internal Audit'
            ]);

        // Update sub menu 108 to point directly to internal-audit
        DB::table('t100_menus')
            ->where('id', 108)
            ->update([
                'menu' => 'internal-audit',
                'menu_name' => 'Internal Audit Form'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert main menu 107
        DB::table('t100_menus')
            ->where('id', 107)
            ->update([
                'menu' => 'genba-internal',
                'menu_name' => 'Genba Internal'
            ]);

        // Revert sub menu 108
        DB::table('t100_menus')
            ->where('id', 108)
            ->update([
                'menu' => 'genba-internal/internal-audit',
                'menu_name' => 'Internal Audit'
            ]);
    }
};
