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
        Schema::create('specifications', function (Blueprint $table) {
            $table->id();
            // Name and value combined should be unique, e.g. "Material: Cotton", "Material: Polyester"
            // A single product might have "Material: Cotton" and "Storage: 128GB"
            // Thus 'name' alone is not unique.
            $table->string('name'); // e.g., Material, Capacity
            $table->string('value'); // e.g., Cotton, 128GB
            $table->timestamps();

            $table->unique(['name', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('specifications');
    }
};
