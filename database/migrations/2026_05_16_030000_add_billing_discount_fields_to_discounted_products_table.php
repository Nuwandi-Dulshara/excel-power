<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('discounted_products', function (Blueprint $table) {
            if (! Schema::hasColumn('discounted_products', 'discount_type')) {
                $table->enum('discount_type', ['fixed', 'percentage'])
                    ->default('percentage')
                    ->after('discount_price');
            }

            if (! Schema::hasColumn('discounted_products', 'discount_value')) {
                $table->decimal('discount_value', 12, 2)
                    ->default(0)
                    ->after('discount_type');
            }

            if (! Schema::hasColumn('discounted_products', 'start_date')) {
                $table->date('start_date')->nullable()->after('discount_value');
            }

            if (! Schema::hasColumn('discounted_products', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('discounted_products', function (Blueprint $table) {
            foreach (['end_date', 'start_date', 'discount_value', 'discount_type'] as $column) {
                if (Schema::hasColumn('discounted_products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
