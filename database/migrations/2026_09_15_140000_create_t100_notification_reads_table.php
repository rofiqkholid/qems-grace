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
        if (Schema::hasTable('t100_notification')) {
            // Make user_id nullable on t100_notification
            try {
                DB::statement('ALTER TABLE t100_notification ALTER COLUMN user_id BIGINT NULL');
            } catch (\Throwable $e) {
                // Ignore if already nullable or different DB engine syntax
            }
        }

        if (!Schema::hasTable('t100_notification_reads')) {
            Schema::create('t100_notification_reads', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('notification_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamp('created_at')->nullable();

                $table->unique(['notification_id', 'user_id']);
                $table->index(['user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t100_notification_reads');
    }
};
