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
            $table->uuid()->unique();
            $table->string('order_number', 30)->nullable()->unique();
            $table->string('invoice_number', 30)->nullable()->unique();
            $table->foreignId('customer_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('pending');
            $table->date('order_date');

            $table->string('discount_type', 10)->nullable();
            $table->decimal('discount_value', 12, 2)->default(0);

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('taxable_amount', 12, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'completed_at']);
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
