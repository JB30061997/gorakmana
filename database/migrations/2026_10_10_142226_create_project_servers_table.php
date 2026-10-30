<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_servers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('server_id')
                ->constrained()
                ->restrictOnDelete();

            $table->enum('environment', [
                'production',
                'staging',
                'development',
                'testing'
            ])->default('production');

            $table->string('deployment_path')->nullable();
            $table->string('application_url')->nullable();
            $table->string('database_name')->nullable();

            $table->unsignedSmallInteger('ssh_port')->default(22);

            $table->date('deployed_at')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'migrated'
            ])->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique([
                'project_id',
                'server_id',
                'environment'
            ], 'project_server_environment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_servers');
    }
};