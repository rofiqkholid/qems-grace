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
        Schema::table('CsAuditCar', function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('requirement_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsAuditCar', function (Blueprint $table) {
            $table->dropColumn(['department', 'requirement_no']);
        });
    }
};
