<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $table = 'order_details';
    protected $primaryKey = 'order_detail_id';

    protected $fillable = [
        'order_id', 'service_type_id',
        'price_per_kg', 'weight_kg', 'express_fee',
    ];

    protected $casts = [
        'price_per_kg' => 'decimal:2',
        'weight_kg'    => 'decimal:2',
        'express_fee'  => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'order_id', 'order_id');
    }

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'service_type_id');
    }
}