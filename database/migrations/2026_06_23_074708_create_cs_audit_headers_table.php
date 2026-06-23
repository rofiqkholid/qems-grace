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
            $table->date('audit_date');
            $table->string('auditee');
            $table->string('auditor_names'); // Auditor conducting the session
            $table->string('auditee_dept');  // Department code
            $table->string('status')->default('Scheduled'); // 'Scheduled', 'Done'
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
