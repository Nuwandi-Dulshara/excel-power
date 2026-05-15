<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounted_products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->integer('current_quantity');
            $table->integer('discount_quantity');

            $table->decimal('price_received', 12, 2);
            $table->decimal('our_price', 12, 2);
            $table->string('supplier_name')->nullable();

            $table->decimal('discount_percentage', 8, 2);
            $table->decimal('maximum_allowed_discount_percentage', 8, 2);
            $table->decimal('discount_price', 12, 2);

            $table->text('reason')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounted_products');
    }
};