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
        Schema::create('CsAuditHeader', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->nullable()->constrained('CsAuditSchedule')->onDelete('set null');
            $table->date('audit_date');
            $table->string('auditor_names'); // Auditor conducting the session
            $table->string('auditee_dept');  // Department code
            $table->string('status')->default('Draft'); // 'Draft', 'Submitted', 'Verification', 'Closed'
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
        Schema::dropIfExists('CsAuditHeader');
    }
};
