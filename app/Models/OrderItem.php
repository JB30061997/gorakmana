<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'service_id', 'description',
        'start_date', 'end_date', 'quantity',
        'unit_price_ht', 'tva_rate',
        'amount_ht', 'amount_tva', 'amount_ttc',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'quantity' => 'decimal:2',
            'unit_price_ht' => 'decimal:2',
            'tva_rate' => 'decimal:2',
            'amount_ht' => 'decimal:2',
            'amount_tva' => 'decimal:2',
            'amount_ttc' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}