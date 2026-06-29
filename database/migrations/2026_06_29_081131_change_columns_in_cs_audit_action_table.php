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
            if (Schema::hasColumn('CsAuditAction', 'causal_factor')) {
                $table->dropColumn('causal_factor');
            }
            $table->text('why_one')->nullable();
            $table->text('why_two')->nullable();
            $table->text('why_three')->nullable();
            $table->text('why_four')->nullable();
            $table->text('why_five')->nullable();
            $table->text('root_cause')->nullable();
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
            $table->text('causal_factor')->nullable();
            $table->dropColumn(['why_one', 'why_two', 'why_three', 'why_four', 'why_five', 'root_cause']);
        });
    }
};
