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
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->string('location')->nullable()->after('product_id');
            
            // Add unique constraint for warehouse + product + location
            $table->unique(['warehouse_id', 'product_id', 'location'], 'warehouse_product_location_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouse_stocks', function (Blueprint $table) {
            $table->dropUnique('warehouse_product_location_unique');
            $table->dropColumn('location');
        });
    }
};
