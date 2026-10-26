<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaundryOrderRequest;
use App\Http\Requests\UpdateLaundryOrderRequest;
use App\Models\LaundryOrder;
use App\Models\ServiceType;
use App\Services\DomainService;

class LaundryOrderController extends Controller
{
    // Inject the Domain Service
    public function __construct(private DomainService $service) {}

    public function index()
    {
        // Eager load relationships and paginate
        $orders = LaundryOrder::with(['customer', 'orderDetails.serviceType', 'delivery'])
                              ->latest('order_id')
                              ->paginate(10);
        return response()->json($orders);
    }

    public function create()
    {
        // Return necessary dependency data for creation
        return response()->json([
            'service_types' => ServiceType::all(),
        ]);
    }

    public function store(StoreLaundryOrderRequest $request)
    {
        // Validation passes, delegate to Service
        $order = $this->service->createLaundryOrder($request->validated());
        return response()->json($order, 201);
    }

    public function update(UpdateLaundryOrderRequest $request, $id)
    {
        // Validation passes, delegate to Service
        $order = $this->service->updateLaundryOrder((int) $id, $request->validated());
        return response()->json($order);
    }
}