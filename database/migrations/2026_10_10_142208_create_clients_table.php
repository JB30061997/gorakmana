<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            $table->enum('type', ['company', 'individual'])
                ->default('company');

            $table->string('code', 30)->unique();
            $table->string('raison_sociale');
            $table->string('ice', 30)->nullable()->index();
            $table->string('identifiant_fiscal', 30)->nullable();
            $table->string('rc', 50)->nullable();
            $table->string('taxe_professionnelle', 50)->nullable();

            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone', 30)->nullable();
            $table->string('website')->nullable();

            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('pays')->default('Maroc');

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
