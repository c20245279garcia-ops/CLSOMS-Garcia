<?php

namespace App\Repositories;

use App\Models\LaundryOrder;

class LaundryOrderRepository
{
    public function createOrder(array $orderData, array $itemsData): LaundryOrder
    {
        // 1. Create the main order
        $order = LaundryOrder::create($orderData);

        // 2. Loop through the items and create the order details
        foreach ($itemsData as $item) {
            $order->orderDetails()->create($item);
        }

        return $order;
    }
}