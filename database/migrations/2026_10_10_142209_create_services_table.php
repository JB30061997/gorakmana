<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->string('code', 30)->unique();
            $table->string('name');
            $table->text('description')->nullable();

            $table->enum('billing_type', [
                'one_time',
                'monthly',
                'quarterly',
                'yearly'
            ])->default('one_time');

            $table->decimal('default_price_ht', 15, 2)->default(0);
            $table->decimal('default_tva_rate', 5, 2)->default(20);
            $table->string('currency', 3)->default('MAD');

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
