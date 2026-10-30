<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('provider')->nullable();
            $table->string('provider_reference')->nullable();

            $table->enum('type', [
                'vps', 'dedicated', 'shared', 'cloud', 'other'
            ])->default('vps');

            $table->string('ip_address', 45)->nullable();
            $table->string('hostname')->nullable();
            $table->string('operating_system')->nullable();

            $table->unsignedInteger('cpu_cores')->nullable();
            $table->unsignedInteger('ram_mb')->nullable();
            $table->unsignedInteger('storage_gb')->nullable();

            $table->decimal('cost_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('MAD');

            $table->enum('billing_cycle', [
                'monthly', 'quarterly', 'yearly', 'other'
            ])->default('monthly');

            $table->date('purchase_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->boolean('auto_renew')->default(false);

            $table->enum('status', [
                'active', 'suspended', 'expired', 'maintenance'
            ])->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};