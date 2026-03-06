<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('return_type', ['full', 'partial'])->nullable();
            $table->enum('reason', ['defective', 'wrong_item', 'not_as_described', 'size_issue', 'quality_issue', 'other'])->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'shipping', 'received', 'refunded'])->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->enum('refund_status', ['pending', 'completed', 'failed'])->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->text('custom_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('rejected_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
