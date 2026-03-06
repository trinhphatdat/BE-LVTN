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
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('title')->nullable();
            $table->string('thumbnail');
            $table->text('description')->nullable();
            $table->enum('product_type', ['male', 'female', 'couple']);
            $table->string('material', 100);
            $table->decimal('min_price', 10, 2);
            $table->decimal('max_price', 10, 2);
            $table->boolean('has_discount');
            $table->integer('max_discount');
            $table->boolean('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
