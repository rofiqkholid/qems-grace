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
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('username')->unique();
                $table->string('full_name')->nullable();
                $table->string('call_name')->nullable();
                $table->string('email')->nullable();
                $table->integer('gender_id')->nullable();
                $table->string('phone_num')->nullable();
                $table->string('password');
                $table->string('epicor_password')->nullable();
                $table->string('signature')->nullable();
                $table->string('avatar')->nullable();
                $table->integer('role_id')->nullable();
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->integer('status_id')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
