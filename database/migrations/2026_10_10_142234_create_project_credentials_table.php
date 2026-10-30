<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');

            $table->enum('type', [
                'admin',
                'database',
                'api',
                'hosting',
                'email',
                'other'
            ])->default('admin');

            $table->string('login_url')->nullable();
            $table->string('username')->nullable();

            $table->longText('encrypted_secret')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_credentials');
    }
};