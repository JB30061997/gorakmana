<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('infrastructure_renewals', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('domain_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->string('title');

            $table->enum('type', [
                'server',
                'domain',
                'ssl',
                'hosting',
                'license',
                'other'
            ]);

            $table->date('due_date');

            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('MAD');

            $table->enum('status', [
                'pending',
                'paid',
                'overdue',
                'cancelled'
            ])->default('pending');

            $table->date('paid_at')->nullable();

            $table->boolean('auto_renew')->default(false);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('infrastructure_renewals');
    }
};