<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_bottle_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('min_qty', 10, 2)->comment('Minimum ML for this bottle');
            $table->decimal('max_qty', 10, 2)->comment('Maximum ML for this bottle');
            $table->foreignId('bottle_product_id')->constrained('products')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bottle_mappings');
    }
};
