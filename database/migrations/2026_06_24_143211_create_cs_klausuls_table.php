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
        Schema::create('CsKlausul', function (Blueprint $table) {
            $table->id();
            $table->string('clause_no');
            $table->string('clause_name');
            $table->timestamps();
        });

        // Seed default clauses
        DB::table('CsKlausul')->insert([
            ['clause_no' => 'IATF 16949 - 4.4.1.2', 'clause_name' => 'Product safety', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 7.2.3', 'clause_name' => 'Internal auditor competency', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 8.5.1', 'clause_name' => 'Control of production and service provision', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 8.5.1.1', 'clause_name' => 'Control plan', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 8.5.1.2', 'clause_name' => 'Standardised work - operator instructions and visual standards', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 8.5.1.3', 'clause_name' => 'Verification of job set-ups', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'IATF 16949 - 8.5.1.5', 'clause_name' => 'Total productive maintenance', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'ISO 9001 - 8.5.2', 'clause_name' => 'Identification and traceability', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'ISO 9001 - 8.7', 'clause_name' => 'Control of nonconforming outputs', 'created_at' => now(), 'updated_at' => now()],
            ['clause_no' => 'ISO 9001 - 9.2', 'clause_name' => 'Internal audit', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('CsKlausul');
    }
};
