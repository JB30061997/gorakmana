<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('service_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('description');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price_ht', 15, 2);

            $table->decimal('tva_rate', 5, 2)->default(20);

            $table->decimal('amount_ht', 15, 2)->default(0);
            $table->decimal('amount_tva', 15, 2)->default(0);
            $table->decimal('amount_ttc', 15, 2)->default(0);

            $table->timestamps();

            $table->index(['order_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};