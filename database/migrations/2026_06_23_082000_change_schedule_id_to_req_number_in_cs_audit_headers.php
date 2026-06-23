<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Safely drop index and foreign key using raw query catches
        try {
            DB::statement('ALTER TABLE CsAuditHeader DROP CONSTRAINT csauditheader_schedule_id_foreign');
        } catch (\Exception $e) {
            // Ignore if constraint doesn't exist
        }

        try {
            DB::statement('DROP INDEX CsAuditHeader.csauditheader_schedule_id_index');
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }

        Schema::table('CsAuditHeader', function (Blueprint $table) {
            // Drop schedule_id column
            if (Schema::hasColumn('CsAuditHeader', 'schedule_id')) {
                $table->dropColumn('schedule_id');
            }

            // Add req_number column
            if (!Schema::hasColumn('CsAuditHeader', 'req_number')) {
                $table->string('req_number', 100)->nullable();
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
        Schema::table('CsAuditHeader', function (Blueprint $table) {
            if (Schema::hasColumn('CsAuditHeader', 'req_number')) {
                $table->dropColumn('req_number');
            }
            if (!Schema::hasColumn('CsAuditHeader', 'schedule_id')) {
                $table->unsignedBigInteger('schedule_id')->nullable();
            }
        });
    }
};
