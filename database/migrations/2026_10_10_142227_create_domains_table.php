<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('server_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('domain_name')->unique();
            $table->string('registrar')->nullable();

            $table->date('registration_date')->nullable();
            $table->date('expiration_date')->nullable();

            $table->boolean('auto_renew')->default(false);

            $table->string('dns_provider')->nullable();
            $table->string('nameservers')->nullable();

            $table->boolean('ssl_enabled')->default(false);
            $table->date('ssl_expiration_date')->nullable();

            $table->decimal('renewal_cost', 15, 2)->nullable();
            $table->string('currency', 3)->default('MAD');

            $table->enum('status', [
                'active',
                'expired',
                'pending',
                'transferred'
            ])->default('active');

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('expiration_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};