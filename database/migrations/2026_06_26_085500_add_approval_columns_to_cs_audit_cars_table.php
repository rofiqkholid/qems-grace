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
            if (!Schema::hasColumn('CsAuditCar', 'status')) {
                $table->string('status')->default('Open');
            }
            if (!Schema::hasColumn('CsAuditCar', 'due_date')) {
                $table->date('due_date')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'evidence_file_path')) {
                $table->string('evidence_file_path')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'completion_date')) {
                $table->date('completion_date')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'dept_head_nik')) {
                $table->string('dept_head_nik')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'dept_head_approved_at')) {
                $table->timestamp('dept_head_approved_at')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'auditor_nik')) {
                $table->string('auditor_nik')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'auditor_verified_at')) {
                $table->timestamp('auditor_verified_at')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'auditor_comments')) {
                $table->text('auditor_comments')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'qmr_nik')) {
                $table->string('qmr_nik')->nullable();
            }
            if (!Schema::hasColumn('CsAuditCar', 'qmr_approved_at')) {
                $table->timestamp('qmr_approved_at')->nullable();
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
        Schema::table('CsAuditCar', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'due_date',
                'evidence_file_path',
                'completion_date',
                'dept_head_nik',
                'dept_head_approved_at',
                'auditor_nik',
                'auditor_verified_at',
                'auditor_comments',
                'qmr_nik',
                'qmr_approved_at'
            ]);
        });
    }
};
