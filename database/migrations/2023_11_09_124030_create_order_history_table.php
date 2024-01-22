<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('order_id');
            $table->boolean('order_status')->nullable();
            $table->integer('department_id')->nullable();
            $table->integer('user_type')->nullable();
            $table->integer('typesofwork_id')->nullable();
            $table->datetime('scan_date')->nullable();
            $table->datetime('receive_time')->nullable();
            $table->string('receive_switch',255)->nullable();
            $table->longText('late_receive_reason')->nullable();
            $table->datetime('issue_time')->nullable(); 
            $table->string('switch_type',255)->nullable();
            $table->longText('reason_for_late')->nullable();         
            $table->foreign('user_id')->references('id')->on('admins')->cascadeOnDelete()->cascadeOnUpdate(); 
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete()->cascadeOnUpdate(); 
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
        Schema::dropIfExists('order_history');
    }
}
