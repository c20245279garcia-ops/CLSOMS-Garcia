<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaundryOrder extends Model
{
    protected $table = 'laundry_orders';

    protected $fillable = [
        'customer_id',
        'status',
        'pickup_date',
        'express_service',
        'express_fee',
        'total_amount',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'express_service' => 'boolean',
        'express_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected $appends = ['grand_total'];

    public function getGrandTotalAttribute(): ?string
    {
        if (!isset($this->attributes['total_amount'])) {
            return null;
        }

        return number_format((float) $this->attributes['total_amount'], 2, '.', '');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'laundry_order_id', 'id');
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'laundry_order_id', 'id');
    }
}