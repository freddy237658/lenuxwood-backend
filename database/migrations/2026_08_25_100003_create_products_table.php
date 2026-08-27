<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('price_unit')->nullable(); // ex: "/m²" pour sols et plafonds
            $table->string('essence')->nullable();
            $table->string('finish')->nullable();
            $table->string('dimensions')->nullable();
            $table->string('manufacturing_delay')->nullable();
            $table->string('warranty')->nullable();
            $table->string('stock')->nullable();
            $table->string('tag')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
