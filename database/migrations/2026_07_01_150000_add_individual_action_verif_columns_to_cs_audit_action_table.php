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
            $table->string('corrective_action_one_verif')->nullable()->after('corrective_path_three');
            $table->string('corrective_action_two_verif')->nullable()->after('corrective_action_one_verif');
            $table->string('corrective_action_three_verif')->nullable()->after('corrective_action_two_verif');
            
            $table->string('preventive_action_one_verif')->nullable()->after('preventive_path_three');
            $table->string('preventive_action_two_verif')->nullable()->after('preventive_action_one_verif');
            $table->string('preventive_action_three_verif')->nullable()->after('preventive_action_two_verif');
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
            $table->dropColumn([
                'corrective_action_one_verif',
                'corrective_action_two_verif',
                'corrective_action_three_verif',
                'preventive_action_one_verif',
                'preventive_action_two_verif',
                'preventive_action_three_verif',
            ]);
        });
    }
};
