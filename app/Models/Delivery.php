<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    protected $table = 'deliveries';
    protected $primaryKey = 'delivery_id';

    protected $fillable = ['order_id', 'delivery_date'];

    protected $casts = ['delivery_date' => 'date'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'order_id', 'order_id');
    }
}