<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('CsAuditApprove', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_car_id')->index();
            $table->string('corrective_action_one_verif')->nullable();
            $table->string('corrective_action_two_verif')->nullable();
            $table->string('corrective_action_three_verif')->nullable();
            $table->string('preventive_action_one_verif')->nullable();
            $table->string('preventive_action_two_verif')->nullable();
            $table->timestamp('superior_approved_at')->nullable();
            $table->timestamp('auditor_approved_at')->nullable();
            $table->timestamp('qmr_approved_at')->nullable();
            $table->string('preventive_action_three_verif')->nullable();
            $table->string('root_cause_verif')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('CsAuditApprove');
    }
};
