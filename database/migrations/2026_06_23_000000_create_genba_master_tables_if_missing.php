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
        // 1. GenbaDept
        if (!Schema::hasTable('GenbaDept')) {
            Schema::create('GenbaDept', function (Blueprint $table) {
                $table->string('Key1', 50)->primary();
                $table->string('Desc', 255)->nullable();
                $table->tinyInteger('CheckBox01')->default(0);
                $table->timestamps();
            });
        }

        // 2. Genba_Area
        if (!Schema::hasTable('Genba_Area')) {
            Schema::create('Genba_Area', function (Blueprint $table) {
                $table->increments('SysID');
                $table->string('Area_name', 255)->nullable();
                $table->string('Process', 255)->nullable();
                $table->timestamps();
            });
        }

        // 3. GenbaCategory
        if (!Schema::hasTable('GenbaCategory')) {
            Schema::create('GenbaCategory', function (Blueprint $table) {
                $table->increments('SysID');
                $table->string('Category', 255)->nullable();
                $table->string('Description', 255)->nullable();
                $table->timestamps();
            });
        }

        // 4. GenbaProcAudit
        if (!Schema::hasTable('GenbaProcAudit')) {
            Schema::create('GenbaProcAudit', function (Blueprint $table) {
                $table->increments('SysID');
                $table->dateTime('Date')->nullable();
                $table->string('Area_Checked', 255)->nullable();
                $table->string('Auditor', 255)->nullable();
                $table->integer('Category_id')->nullable();
                $table->string('station', 255)->nullable();
                $table->string('process', 255)->nullable();
                $table->string('is_team', 255)->nullable();
                $table->integer('status')->default(4);
                $table->tinyInteger('IsDelete')->default(0);
                $table->timestamps();
            });
        }

        // 5. GenbaProcAuditDtl
        if (!Schema::hasTable('GenbaProcAuditDtl')) {
            Schema::create('GenbaProcAuditDtl', function (Blueprint $table) {
                $table->increments('SysID');
                $table->integer('genba_id')->nullable();
                $table->integer('scope_id')->nullable();
                $table->integer('check_item_id')->nullable();
                $table->dateTime('due_date')->nullable();
                $table->string('result', 50)->nullable();
                $table->integer('user_id')->nullable();
                $table->text('findings')->nullable();
                $table->string('Path', 255)->nullable();
                $table->string('asign_to', 255)->nullable();
                $table->string('asign_to_dept', 50)->nullable();
                $table->string('asign_to_dept_name', 255)->nullable();
                $table->string('type', 100)->nullable();
                $table->string('area_detail', 255)->nullable();
                $table->text('corrective_action')->nullable();
                $table->text('evidence')->nullable();
                $table->string('status', 100)->nullable();
                $table->dateTime('complete_date')->nullable();
                $table->text('execution_comment')->nullable();
                $table->text('execution_path')->nullable();
                $table->string('verification_result', 50)->nullable();
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
        Schema::dropIfExists('GenbaProcAuditDtl');
        Schema::dropIfExists('GenbaProcAudit');
        Schema::dropIfExists('GenbaCategory');
        Schema::dropIfExists('Genba_Area');
        Schema::dropIfExists('GenbaDept');
    }
};
