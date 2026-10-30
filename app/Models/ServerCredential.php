<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServerCredential extends Model
{
    protected $fillable = [
        'server_id',
        'name',
        'type',
        'host',
        'port',
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
            'port' => 'integer',
            'last_accessed_at' => 'datetime',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
