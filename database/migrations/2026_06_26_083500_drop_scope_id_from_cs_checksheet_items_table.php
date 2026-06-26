<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('CsChecksheetItem', function (Blueprint $table) {
            if (Schema::hasColumn('CsChecksheetItem', 'scope_id')) {
                $table->dropColumn('scope_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsChecksheetItem', function (Blueprint $table) {
            $table->integer('scope_id')->nullable();
        });
    }
};
