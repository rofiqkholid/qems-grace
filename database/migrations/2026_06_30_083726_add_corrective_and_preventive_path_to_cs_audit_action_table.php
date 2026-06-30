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
            $table->text('corrective_path')->nullable()->after('corrective_action');
            $table->text('preventive_path')->nullable()->after('preventive_action');
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
            $table->dropColumn(['corrective_path', 'preventive_path']);
        });
    }
};
