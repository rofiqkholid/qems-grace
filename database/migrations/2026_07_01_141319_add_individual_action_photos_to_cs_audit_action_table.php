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
            $table->string('corrective_path_one')->nullable()->after('corrective_action_three');
            $table->string('corrective_path_two')->nullable()->after('corrective_path_one');
            $table->string('corrective_path_three')->nullable()->after('corrective_path_two');
            
            $table->string('preventive_path_one')->nullable()->after('preventive_action_three');
            $table->string('preventive_path_two')->nullable()->after('preventive_path_one');
            $table->string('preventive_path_three')->nullable()->after('preventive_path_two');
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
                'corrective_path_one',
                'corrective_path_two',
                'corrective_path_three',
                'preventive_path_one',
                'preventive_path_two',
                'preventive_path_three',
            ]);
        });
    }
};
