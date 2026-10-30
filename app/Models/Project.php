<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'code', 'name', 'description',
        'technology', 'repository_url', 'production_url',
        'start_date', 'end_date', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function servers(): BelongsToMany
    {
        return $this->belongsToMany(Server::class, 'project_servers')
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
        return $this->hasMany(ProjectCredential::class);
    }
}