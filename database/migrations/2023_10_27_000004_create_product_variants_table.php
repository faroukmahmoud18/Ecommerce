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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('size_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('specification_id')->nullable()->constrained('specifications')->onDelete('set null');
            $table->decimal('price', 8, 2);
            $table->integer('stock');
            $table->string('sku')->unique()->nullable();
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
        Schema::dropIfExists('product_variants');
    }
};
