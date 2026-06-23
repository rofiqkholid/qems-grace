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
        Schema::create('CsChecksheetItem', function (Blueprint $table) {
            $table->id();
            $table->string('clause_number')->nullable(); // e.g. "IATF 16949 - 8.5.1"
            $table->text('requirement_desc');            // Clause requirement / checksheet question
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('CsChecksheetItem');
    }
};
