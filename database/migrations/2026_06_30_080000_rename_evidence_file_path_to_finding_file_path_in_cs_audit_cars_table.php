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
        Schema::table('CsAuditCar', function (Blueprint $table) {
            // Check if column exists before renaming to prevent errors
            if (Schema::hasColumn('CsAuditCar', 'evidence_file_path')) {
                $table->renameColumn('evidence_file_path', 'finding_file_path');
            } else if (!Schema::hasColumn('CsAuditCar', 'finding_file_path')) {
                $table->string('finding_file_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsAuditCar', function (Blueprint $table) {
            if (Schema::hasColumn('CsAuditCar', 'finding_file_path')) {
                $table->renameColumn('finding_file_path', 'evidence_file_path');
            }
        });
    }
};
