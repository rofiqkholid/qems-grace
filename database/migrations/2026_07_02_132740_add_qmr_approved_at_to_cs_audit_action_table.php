<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->timestamp('qmr_approved_at')->nullable()->after('auditor_approved_at');
        });
    }

    public function down()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->dropColumn('qmr_approved_at');
        });
    }
};
