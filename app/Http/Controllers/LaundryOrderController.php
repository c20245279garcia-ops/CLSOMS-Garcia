<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaundryOrderRequest;
use App\Models\LaundryOrder;
use App\Models\ServiceType;
use App\Services\LaundryOrderService;
use Inertia\Inertia;

class LaundryOrderController extends Controller
{
    public function __construct(private LaundryOrderService $orderService) {}

    public function create()
    {
        return Inertia::render('laundry/Create', [
            'serviceTypes' => ServiceType::query()
                ->orderBy('name')
                ->get(['id', 'name', 'price_per_kg']),
            'expressFee' => LaundryOrderService::EXPRESS_SERVICE_FEE,
        ]);
    }

    public function index()
    {
        $orders = LaundryOrder::with(['customer', 'delivery'])->latest()->get();

        return Inertia::render('laundry/Index', [
            'orders' => $orders
        ]);
    }

    public function store(StoreLaundryOrderRequest $request)
    {
        $this->orderService->processNewOrder($request->validated());

        return redirect()->back()->with('success', 'Laundry order created successfully!');
    }
}