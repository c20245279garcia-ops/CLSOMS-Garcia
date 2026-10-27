<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $table = 'deliveries';

    protected $fillable = ['laundry_order_id', 'delivery_date', 'delivery_status', 'delivered_at'];

    protected $casts = [
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'laundry_order_id', 'id');
    }
}