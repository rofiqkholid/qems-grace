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
        // 1. Delete dependent data first to avoid FK constraint errors
        DB::table('CsAuditCar')->delete();
        DB::table('CsAuditDetail')->delete();
        DB::table('CsChecksheetItem')->delete();

        // 2. Modify CsChecksheetItem columns
        Schema::table('CsChecksheetItem', function (Blueprint $table) {
            $table->dropColumn('clause_number');
            $table->dropColumn('requirement_desc');

            $table->text('check_item_idn');
            $table->text('check_item_en');
            $table->string('department');
            $table->integer('scope_id')->nullable();
            $table->string('scope_item')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsChecksheetItem', function (Blueprint $table) {
            $table->dropColumn(['check_item_idn', 'check_item_en', 'department', 'scope_id', 'scope_item']);
            $table->string('clause_number')->nullable();
            $table->text('requirement_desc')->nullable();
        });
    }
};
