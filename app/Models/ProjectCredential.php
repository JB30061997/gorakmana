<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCredential extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'type',
        'login_url',
        'username',
        'encrypted_secret',
        'notes',
        'created_by',
    ];

    protected $hidden = [
        'encrypted_secret',
    ];

    protected function casts(): array
    {
        return [
            'encrypted_secret' => 'encrypted',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
