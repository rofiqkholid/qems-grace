<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('t100_menus')) {
            Schema::create('t100_menus', function (Blueprint $table) {
                $table->integer('id')->primary();
                $table->smallInteger('sequence_id');
                $table->smallInteger('level_menu_id');
                $table->smallInteger('group_id');
                $table->smallInteger('sub_group_id');
                $table->string('menu', 100);
                $table->string('menu_name', 100);
                $table->text('icon');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t100_menus');
    }
};
