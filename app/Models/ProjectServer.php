<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectServer extends Model
{
    protected $table = 'project_servers';

    protected $fillable = [
        'project_id',
        'server_id',
        'environment',
        'deployment_path',
        'application_url',
        'database_name',
        'ssh_port',
        'deployed_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'ssh_port' => 'integer',
            'deployed_at' => 'date',
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
}