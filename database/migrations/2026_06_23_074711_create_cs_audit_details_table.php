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
        Schema::create('CsAuditDetail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_header_id')->constrained('CsAuditHeader')->onDelete('cascade');
            $table->foreignId('checksheet_item_id')->constrained('CsChecksheetItem');
            $table->string('judgment'); // 'OK', 'OFI', 'Mayor', 'Minor'
            $table->text('evidence')->nullable(); // Column evidence or detail of finding
            $table->string('finding_photo_path')->nullable(); // Attachment path
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
        Schema::dropIfExists('CsAuditDetail');
    }
};
