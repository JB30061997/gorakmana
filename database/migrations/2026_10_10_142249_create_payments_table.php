<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('invoice_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('reference', 50)->unique();

            $table->date('payment_date');

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('MAD');

            $table->enum('payment_method', [
                'cash',
                'bank_transfer',
                'cheque',
                'card',
                'other'
            ])->default('bank_transfer');

            $table->enum('status', [
                'pending',
                'completed',
                'failed',
                'refunded'
            ])->default('completed');

            $table->string('transaction_reference')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['invoice_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};