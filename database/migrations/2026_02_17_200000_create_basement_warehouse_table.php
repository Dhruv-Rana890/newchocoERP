<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create basement_warehouse table for warehouse-wise basement stock tracking
     * Similar to product_warehouse table structure
     */
    public function up(): void
    {
        Schema::create('basement_warehouse', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('basement_id');
            $table->integer('warehouse_id');
            $table->double('qty')->default(0);
            $table->timestamps();
            
            // Unique constraint: one basement can have one qty per warehouse
            $table->unique(['basement_id', 'warehouse_id'], 'basement_warehouse_unique');
            
            // Indexes for faster queries
            $table->index('basement_id');
            $table->index('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('basement_warehouse');
    }
};
