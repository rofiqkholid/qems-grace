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
        Schema::table('KPIList', function (Blueprint $table) {
            $table->string('source_data', 50)->nullable()->default('Manual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('KPIList', function (Blueprint $table) {
            $table->dropColumn('source_data');
        });
    }
};
