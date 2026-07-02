<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('CsAuditCar', function (Blueprint $table) {
            $table->dropColumn(['completion_date', 'approved_by_superior']);
        });
    }

    public function down()
    {
        Schema::table('CsAuditCar', function (Blueprint $table) {
            $table->date('completion_date')->nullable();
            $table->integer('approved_by_superior')->default(0);
        });
    }
};
