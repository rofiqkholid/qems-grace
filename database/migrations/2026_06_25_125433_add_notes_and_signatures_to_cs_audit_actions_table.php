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
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->text('notes')->nullable();
            $table->string('auditee_name')->nullable();
            $table->string('auditee_superior_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->dropColumn(['notes', 'auditee_name', 'auditee_superior_name']);
        });
    }
};
