<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLaundryOrderRequest;
use App\Services\LaundryOrderService;
use App\Models\LaundryOrder;
use App\Models\ServiceType;
use Inertia\Inertia;

class LaundryOrderController extends Controller
{
    protected $orderService;

    public function __construct(LaundryOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function store(StoreLaundryOrderRequest $request)
    {
        // Pass the validated data to the Service
        $this->orderService->processNewOrder($request->validated());

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Laundry order created successfully!');
    }

    public function create()
    {
        return Inertia::render('laundry/create', [
            'expressFee' => LaundryOrderService::EXPRESS_SERVICE_FEE,
            'serviceTypes' => ServiceType::query()
                ->orderBy('name')
                ->get(['id', 'name', 'price_per_kg']),
        ]);
    }

    public function index()
    {
        // Fetch all orders with their customer and delivery details
        $orders = LaundryOrder::with(['customer', 'delivery'])->latest()->get();

        // Return the React page and pass the data
        return Inertia::render('laundry/Index', [
            'orders' => $orders
        ]);
    }
}