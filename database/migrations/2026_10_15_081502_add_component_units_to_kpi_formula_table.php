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
        Schema::table('KPIFormula', function (Blueprint $table) {
            for ($i = 1; $i <= 20; $i++) {
                if (!Schema::hasColumn('KPIFormula', 'comp_' . $i . '_unit')) {
                    $table->string('comp_' . $i . '_unit', 50)->nullable();
                }
            }
        });
    }

    public function down()
    {
        Schema::table('KPIFormula', function (Blueprint $table) {
            for ($i = 1; $i <= 20; $i++) {
                if (Schema::hasColumn('KPIFormula', 'comp_' . $i . '_unit')) {
                    $table->dropColumn('comp_' . $i . '_unit');
                }
            }
        });
    }
};
