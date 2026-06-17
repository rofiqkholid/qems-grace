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
        if (!Schema::hasTable('t100_user_menus_permission')) {
            Schema::create('t100_user_menus_permission', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('id_user');
                $table->integer('id_menus');
                $table->tinyInteger('is_view')->nullable()->default(0);
                
                // Foreign keys (optional but good practice)
                $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('id_menus')->references('id')->on('t100_menus')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t100_user_menus_permission');
    }
};
