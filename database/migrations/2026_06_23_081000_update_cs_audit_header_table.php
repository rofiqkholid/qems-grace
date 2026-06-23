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
        Schema::table('CsAuditHeader', function (Blueprint $table) {
            if (!Schema::hasColumn('CsAuditHeader', 'auditee')) {
                $table->string('auditee')->nullable();
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
        Schema::table('CsAuditHeader', function (Blueprint $table) {
            if (Schema::hasColumn('CsAuditHeader', 'auditee')) {
                $table->dropColumn('auditee');
            }
        });
    }
};
