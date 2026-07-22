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
        if (!Schema::hasTable('CsAuditAuditor')) {
            Schema::create('CsAuditAuditor', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_user')->unique();
                $table->boolean('is_auditor')->default(false);
                $table->timestamps();
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
        Schema::dropIfExists('CsAuditAuditor');
    }
};
