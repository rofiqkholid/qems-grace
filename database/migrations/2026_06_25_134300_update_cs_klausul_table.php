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
        Schema::table('CsKlausul', function (Blueprint $table) {
            $table->renameColumn('clause_name', 'clause_title');
        });

        Schema::table('CsKlausul', function (Blueprint $table) {
            $table->text('clauses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsKlausul', function (Blueprint $table) {
            $table->dropColumn('clauses');
        });

        Schema::table('CsKlausul', function (Blueprint $table) {
            $table->renameColumn('clause_title', 'clause_name');
        });
    }
};
