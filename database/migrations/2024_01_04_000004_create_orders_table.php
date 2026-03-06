<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->integer('province_id');
            $table->integer('district_id');
            $table->integer('ward_id');
            $table->string('address')->nullable();
            $table->text('text_note')->nullable();
            $table->text('text_custom_couple')->nullable();
            $table->enum('order_status', ['pending', 'confirmed', 'processing', 'delivering', 'delivered', 'cancelled', 'returning', 'returned'])->nullable();
            $table->decimal('items_total', 10, 2)->nullable();
            $table->decimal('shipping_fee', 10, 2)->nullable();
            $table->decimal('shipping_discount', 10, 2)->nullable();
            $table->unsignedBigInteger('promotion_id')->nullable();
            $table->decimal('promotion_discount', 10, 2)->nullable();
            $table->decimal('total_money', 10, 2)->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0);
            $table->decimal('actual_revenue', 10, 2)->default(0);
            $table->enum('payment_method', ['cod', 'vnpay'])->nullable();
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded', 'failed'])->nullable();
            $table->string('vnpay_transaction_id')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('payment_expires_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->string('ghn_order_code')->nullable();
            $table->string('ghn_sort_code')->nullable();
            $table->string('ghn_status')->nullable();
            $table->text('ghn_status_text')->nullable();
            $table->decimal('ghn_total_fee', 15, 2)->nullable();
            $table->string('ghn_expected_delivery_time')->nullable();
            $table->decimal('ghn_cod_amount', 15, 2)->nullable();
            $table->dateTime('ghn_last_sync_at')->nullable();
            $table->json('ghn_log')->nullable();
            $table->text('ghn_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
