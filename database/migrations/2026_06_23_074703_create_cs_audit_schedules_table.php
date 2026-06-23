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
        Schema::create('CsAuditSchedule', function (Blueprint $table) {
            $table->id();
            $table->string('agenda_name');
            $table->date('schedule_date');
            $table->string('auditor_niks'); // Comma-separated or JSON list of auditors
            $table->string('auditee_dept');  // Department code
            $table->string('status')->default('Scheduled'); // 'Draft', 'Scheduled', 'Cancelled', 'Done'
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
        Schema::dropIfExists('CsAuditSchedule');
    }
};
