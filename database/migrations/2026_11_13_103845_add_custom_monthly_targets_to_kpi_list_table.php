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
            $table->boolean('is_custom_monthly_target')->default(false)->after('target');
            $table->text('monthly_targets')->nullable()->after('is_custom_monthly_target');
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
            $table->dropColumn(['is_custom_monthly_target', 'monthly_targets']);
        });
    }
};
