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
        Schema::dropIfExists('CsAuditCar');

        Schema::create('CsAuditCar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_detail_id')->constrained('CsAuditDetail')->onDelete('cascade');
            $table->string('req_number')->nullable();
            $table->text('check_item')->nullable();
            $table->string('surveillance')->nullable();
            $table->string('external')->nullable();
            $table->string('internal_audit')->nullable();
            $table->string('department')->nullable();
            $table->string('requirement_no')->nullable();
            $table->string('clause_title')->nullable();
            $table->text('clause_text')->nullable();
            $table->string('finding_category')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CsAuditCar');
    }
};
