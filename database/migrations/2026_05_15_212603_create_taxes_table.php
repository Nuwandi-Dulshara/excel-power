<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('tax_name');
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->enum('tax_type', ['inclusive', 'exclusive'])->default('exclusive');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique('tax_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxes');
    }
};