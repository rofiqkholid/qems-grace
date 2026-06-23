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
        Schema::create('CsAuditCar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_detail_id')->constrained('CsAuditDetail')->onDelete('cascade');
            $table->string('car_number')->unique(); // CAR document number
            $table->text('finding_desc'); // Finding description from audit details
            
            // Auditee fields
            $table->text('corrective_action')->nullable();
            $table->text('preventive_action')->nullable();
            $table->string('evidence_file_path')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completion_date')->nullable();
            
            // Status and approvals tracking
            $table->string('status')->default('Open'); // 'Open', 'Under Review', 'Need Verification', 'Closed'
            
            // Approval 1: Dept Head Auditee
            $table->string('dept_head_nik')->nullable();
            $table->timestamp('dept_head_approved_at')->nullable();
            
            // Approval 2: Auditor Verification
            $table->string('auditor_nik')->nullable();
            $table->timestamp('auditor_verified_at')->nullable();
            $table->text('auditor_comments')->nullable();
            
            // Approval 3: QMR Approval for Close
            $table->string('qmr_nik')->nullable();
            $table->timestamp('qmr_approved_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CsAuditCar');
    }
};
