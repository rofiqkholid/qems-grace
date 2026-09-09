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
        if (Schema::hasTable('t100_notification')) {
            Schema::table('t100_notification', function (Blueprint $table) {
                if (!Schema::hasColumn('t100_notification', 'target_type')) {
                    $table->string('target_type')->default('user')->after('user_id');
                }
                if (!Schema::hasColumn('t100_notification', 'target_value')) {
                    $table->string('target_value')->nullable()->after('target_type');
                }
                if (!Schema::hasColumn('t100_notification', 'batch_id')) {
                    $table->string('batch_id')->nullable()->after('id');
                }
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
        if (Schema::hasTable('t100_notification')) {
            Schema::table('t100_notification', function (Blueprint $table) {
                $table->dropColumn(['target_type', 'target_value', 'batch_id']);
            });
        }
    }
};
