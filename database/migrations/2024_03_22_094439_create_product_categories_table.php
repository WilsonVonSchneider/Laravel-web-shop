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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary(); 
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->timestamps();
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign('parent_id')
            ->references('id')
            ->on('product_categories')
            ->onUpdate('cascade')
            ->onDelete('set null');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
