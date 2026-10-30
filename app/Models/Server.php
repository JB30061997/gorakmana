<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'provider', 'provider_reference',
        'type', 'ip_address', 'hostname',
        'operating_system', 'cpu_cores', 'ram_mb',
        'storage_gb', 'cost_amount', 'currency',
        'billing_cycle', 'purchase_date',
        'expiration_date', 'auto_renew', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'cpu_cores' => 'integer',
            'ram_mb' => 'integer',
            'storage_gb' => 'integer',
            'cost_amount' => 'decimal:2',
            'purchase_date' => 'date',
            'expiration_date' => 'date',
            'auto_renew' => 'boolean',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_servers')
            ->withPivot([
                'id', 'environment', 'deployment_path',
                'application_url', 'database_name',
                'ssh_port', 'deployed_at', 'status', 'notes',
            ])
            ->withTimestamps();
    }

    public function projectServers(): HasMany
    {
        return $this->hasMany(ProjectServer::class);
    }

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    public function credentials(): HasMany
    {
        return $this->hasMany(ServerCredential::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(InfrastructureRenewal::class);
    }
}