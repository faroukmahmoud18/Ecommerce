<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('name'); // Item name
            $table->string('image')->nullable(); // Item image URL
            $table->string('size')->nullable(); // Item size
            $table->float('price'); // Item price
            $table->integer('quantity'); // Item quantity
            $table->timestamps();

            // Foreign key to orders table
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            // Foreign key to products table
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
