<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $table = 'service_types';
    protected $primaryKey = 'service_type_id';

    protected $fillable = ['service_type_name', 'price_per_kg'];

    protected $casts = ['price_per_kg' => 'decimal:2'];

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'service_type_id', 'service_type_id');
    }
}