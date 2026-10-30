<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('server_credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('server_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');

            $table->enum('type', [
                'ssh',
                'sftp',
                'ftp',
                'plesk',
                'cpanel',
                'database',
                'api',
                'other'
            ])->default('ssh');

            $table->string('host')->nullable();
            $table->unsignedSmallInteger('port')->nullable();

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
        Schema::dropIfExists('server_credentials');
    }
};