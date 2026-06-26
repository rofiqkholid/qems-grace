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
            // Drop unnecessary approval columns
            $table->dropColumn([
                'dept_head_nik',
                'dept_head_approved_at',
                'auditor_nik',
                'auditor_verified_at',
                'auditor_comments',
                'qmr_nik',
                'qmr_approved_at'
            ]);

            // Add simple approved_by_superior column
            $table->integer('approved_by_superior')->default(0);
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
            $table->dropColumn('approved_by_superior');
            
            $table->string('dept_head_nik')->nullable();
            $table->timestamp('dept_head_approved_at')->nullable();
            $table->string('auditor_nik')->nullable();
            $table->timestamp('auditor_verified_at')->nullable();
            $table->text('auditor_comments')->nullable();
            $table->string('qmr_nik')->nullable();
            $table->timestamp('qmr_approved_at')->nullable();
        });
    }
};
