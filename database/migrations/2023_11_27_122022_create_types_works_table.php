<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types_works', function (Blueprint $table) {
            $table->id();
            $table->string("types_of_works")->nullable();
            $table->unsignedBigInteger('order_id');
            $table->string("order_value")->nullable();
            $table->string("working_hours")->nullable();
            $table->string("working_minutes")->nullable();
            $table->string('working_seconds')->default('00')->nullable();
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
        Schema::dropIfExists('types_works');
    }
}
