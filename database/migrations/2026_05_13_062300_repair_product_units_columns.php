<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $needsUnitName = ! Schema::hasColumn('product_units', 'unit_name');
        $needsShortCode = ! Schema::hasColumn('product_units', 'short_code');

        Schema::table('product_units', function (Blueprint $table) {
            if (! Schema::hasColumn('product_units', 'unit_name')) {
                $table->string('unit_name')->nullable();
            }

            if (! Schema::hasColumn('product_units', 'short_code')) {
                $table->string('short_code', 50)->nullable();
            }

            if (! Schema::hasColumn('product_units', 'description')) {
                $table->text('description')->nullable();
            }

            if (! Schema::hasColumn('product_units', 'status')) {
                $table->string('status')->default('active');
            }
        });

        if ($needsUnitName || $needsShortCode) {
            Schema::table('product_units', function (Blueprint $table) use ($needsUnitName, $needsShortCode) {
                if ($needsUnitName) {
                    $table->unique('unit_name');
                }

                if ($needsShortCode) {
                    $table->unique('short_code');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            $table->dropUnique(['unit_name']);
            $table->dropUnique(['short_code']);
        });

        Schema::table('product_units', function (Blueprint $table) {
            $table->dropColumn([
                'unit_name',
                'short_code',
                'description',
                'status',
            ]);
        });
    }
};
