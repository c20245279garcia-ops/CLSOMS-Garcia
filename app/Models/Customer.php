<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $table = 'customers';

    protected $fillable = ['name', 'phone', 'address'];

    public function laundryOrders(): HasMany
    {
        return $this->hasMany(LaundryOrder::class, 'customer_id', 'id');
    }
}