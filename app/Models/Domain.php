<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Domain extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'server_id',
        'domain_name',
        'registrar',
        'registration_date',
        'expiration_date',
        'auto_renew',
        'dns_provider',
        'nameservers',
        'ssl_enabled',
        'ssl_expiration_date',
        'renewal_cost',
        'currency',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'registration_date' => 'date',
            'expiration_date' => 'date',
            'ssl_expiration_date' => 'date',
            'auto_renew' => 'boolean',
            'ssl_enabled' => 'boolean',
            'renewal_cost' => 'decimal:2',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(InfrastructureRenewal::class);
    }
}