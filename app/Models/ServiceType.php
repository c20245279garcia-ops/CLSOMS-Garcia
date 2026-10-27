<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceType extends Model
{
    protected $table = 'service_types';

    protected $fillable = ['name', 'price_per_kg'];
    protected $casts = ['price_per_kg' => 'decimal:2'];

    public function setNameAttribute(string $name): void
    {
        $this->attributes['name'] = trim($name);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'service_type_id', 'id');
    }
}