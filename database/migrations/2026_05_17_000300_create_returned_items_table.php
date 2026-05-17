<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returned_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('sale_item_id')->nullable()->constrained('sale_items')->cascadeOnUpdate()->nullOnDelete();
            $table->integer('quantity');
            $table->enum('return_type', ['customer_return', 'supplier_return']);
            $table->enum('condition', ['good', 'damaged', 'expired']);
            $table->enum('action_type', ['add_back_to_stock', 'move_to_damage', 'move_to_discount', 'return_to_supplier']);
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returned_items');
    }
};
