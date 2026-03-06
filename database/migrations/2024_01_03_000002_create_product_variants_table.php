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
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->unsignedBigInteger('color_id');
            $table->integer('stock')->nullable();
            $table->integer('defective_stock')->default(0);
            $table->decimal('original_price', 10, 2);
            $table->integer('discount');
            $table->decimal('price', 10, 2);
            $table->string('image_url');
            $table->boolean('status');
            $table->timestamps();

            $table->index('product_id');
            $table->index('size_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
