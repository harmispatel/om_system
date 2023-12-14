<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaskManageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_manage', function (Blueprint $table) {
            $table->id();
            $table->integer('task1_id');
            // $table->foreign('task1_id')->references('id')->on('permissions')->cascadeOnDelete()->cascadeOnUpdate(); 
            $table->integer('task2_id');
            // $table->foreign('task2_id')->references('id')->on('permissions')->cascadeOnDelete()->cascadeOnUpdate(); 
           $table->string("working_hours")->nullable();
            $table->string("working_minutes")->nullable();
            $table->string('working_seconds')->default('00')->nullable();
            $table->integer('types_of_works')->nullable();
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
        Schema::dropIfExists('task_manage');
    }
}
