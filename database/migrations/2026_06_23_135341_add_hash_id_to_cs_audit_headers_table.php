<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('CsAuditHeader', function (Blueprint $table) {
            if (!Schema::hasColumn('CsAuditHeader', 'hash_id')) {
                $table->string('hash_id', 50)->nullable()->unique();
            }
        });

        // Seed existing rows with hash
        $rows = DB::table('CsAuditHeader')->get();
        foreach ($rows as $row) {
            // Generates 9 alphanumeric characters separated by - (e.g., abc-def-123)
            $hash = strtolower(Str::random(3) . '-' . Str::random(3) . '-' . Str::random(3));
            DB::table('CsAuditHeader')->where('id', $row->id)->update(['hash_id' => $hash]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('CsAuditHeader', function (Blueprint $table) {
            if (Schema::hasColumn('CsAuditHeader', 'hash_id')) {
                $table->dropColumn('hash_id');
            }
        });
    }
};
