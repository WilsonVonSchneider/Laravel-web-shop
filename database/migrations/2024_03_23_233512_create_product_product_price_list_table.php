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
        Schema::create('product_product_price_list', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('product_price_list_id');

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');            
            $table->foreign('product_price_list_id')->references('id')->on('product_price_lists')->onDelete('cascade');
            
            $table->float('price'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_product_price_list');
    }
};
