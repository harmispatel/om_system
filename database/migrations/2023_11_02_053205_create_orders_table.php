<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('counter_id')->nullable();
            $table->string('name')->nullable();
            $table->string('mobile')->nullable();
            $table->string('SelectOrder')->default('0');
            $table->float('touch')->nullable()->length(2,2);
            $table->string('gold')->nullable();
            $table->string('metal')->nullable();
            $table->string('orderno')->nullable();
            $table->string('charges')->nullable();
            $table->string('advance')->nullable();
            $table->string('metalwt')->nullable();
            $table->string('deliverydate')->nullable();
            $table->string('handleby')->nullable();
            $table->string('Qrphoto')->nullable();
            $table->string('order_status')->nullable();
            $table->tinyinteger('is_bloked')->nullable();
            $table->text('block_reason')->nullable();
            $table->integer('whos_block_order')->nullable();
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
        Schema::dropIfExists('orders');
    }
}
