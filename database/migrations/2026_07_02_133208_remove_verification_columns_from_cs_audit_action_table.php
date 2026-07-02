<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->dropColumn([
                'corrective_action_one_verif',
                'corrective_action_two_verif',
                'corrective_action_three_verif',
                'preventive_action_one_verif',
                'preventive_action_two_verif',
                'preventive_action_three_verif',
                'root_cause_verif',
                'superior_approved_at',
                'auditor_approved_at',
                'qmr_approved_at'
            ]);
        });
    }

    public function down()
    {
        Schema::table('CsAuditAction', function (Blueprint $table) {
            $table->string('corrective_action_one_verif')->nullable();
            $table->string('corrective_action_two_verif')->nullable();
            $table->string('corrective_action_three_verif')->nullable();
            $table->string('preventive_action_one_verif')->nullable();
            $table->string('preventive_action_two_verif')->nullable();
            $table->string('preventive_action_three_verif')->nullable();
            $table->string('root_cause_verif')->nullable();
            $table->timestamp('superior_approved_at')->nullable();
            $table->timestamp('auditor_approved_at')->nullable();
            $table->timestamp('qmr_approved_at')->nullable();
        });
    }
};
