<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreignId('product_unit_id')
                ->nullable()
                ->constrained('product_units')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->string('variant_name');
            $table->string('size');

            $table->string('barcode')->nullable()->unique();
            $table->string('sku')->nullable()->unique();
            $table->text('description')->nullable();

            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->integer('purchase_quantity')->default(1);
            $table->decimal('cost_price', 12, 2)->nullable();

            $table->decimal('selling_price', 12, 2)->default(0);
            $table->decimal('our_price', 12, 2)->default(0);

            $table->integer('minimum_wholesale_quantity')->nullable();
            $table->decimal('wholesale_price', 12, 2)->nullable();

            $table->integer('opening_stock')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('reorder_level')->nullable();

            $table->date('expiry_date')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
