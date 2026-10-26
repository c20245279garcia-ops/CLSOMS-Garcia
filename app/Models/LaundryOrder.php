<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaundryOrder extends Model
{
    protected $table = 'laundry_orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'customer_id', 'pickup_date', 'laundry_status',
        'grand_total', 'payment_status',
    ];

    protected $casts = [
        'pickup_date'   => 'date',
        'grand_total'   => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'order_id');
    }

    public function delivery(): HasOne
    {
        return $this->hasOne(Delivery::class, 'order_id', 'order_id');
    }
}