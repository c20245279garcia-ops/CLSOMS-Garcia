<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeliveryRequest;
use App\Http\Requests\UpdateDeliveryRequest;
use App\Models\Delivery;
use App\Models\LaundryOrder;
use App\Services\DomainService;

class DeliveryController extends Controller
{
    public function __construct(private DomainService $service) {}

    public function index()
    {
        $deliveries = Delivery::with('order.customer')
                              ->latest('delivery_id')
                              ->paginate(10);
        return response()->json($deliveries);
    }

    public function create()
    {
        // orders without an existing delivery
        $orders = LaundryOrder::doesntHave('delivery')->get(['order_id', 'pickup_date']);
        return response()->json(['orders' => $orders]);
    }

    public function store(StoreDeliveryRequest $request)
    {
        $delivery = $this->service->createDelivery($request->validated());
        return response()->json($delivery, 201);
    }

    public function update(UpdateDeliveryRequest $request, $id)
    {
        $delivery = $this->service->updateDelivery((int) $id, $request->validated());
        return response()->json($delivery);
    }
}