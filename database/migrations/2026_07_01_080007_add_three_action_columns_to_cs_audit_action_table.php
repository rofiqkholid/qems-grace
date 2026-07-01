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
            $table->text('corrective_action_one')->nullable()->after('analyzed_by');
            $table->text('corrective_action_two')->nullable()->after('corrective_action_one');
            $table->text('corrective_action_three')->nullable()->after('corrective_action_two');
            
            $table->text('preventive_action_one')->nullable()->after('corrective_path');
            $table->text('preventive_action_two')->nullable()->after('preventive_action_one');
            $table->text('preventive_action_three')->nullable()->after('preventive_action_two');

            $table->dropColumn(['corrective_action', 'preventive_action']);
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
            $table->text('corrective_action')->nullable()->after('analyzed_by');
            $table->text('preventive_action')->nullable()->after('corrective_path');

            $table->dropColumn([
                'corrective_action_one',
                'corrective_action_two',
                'corrective_action_three',
                'preventive_action_one',
                'preventive_action_two',
                'preventive_action_three',
            ]);
        });
    }
};
