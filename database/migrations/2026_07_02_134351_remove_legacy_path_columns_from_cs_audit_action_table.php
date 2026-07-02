<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->dropColumn(['corrective_path', 'preventive_path']);
        });
    }

    public function down()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->text('corrective_path')->nullable();
            $table->text('preventive_path')->nullable();
        });
    }
};
