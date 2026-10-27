<?php

namespace App\Services;

use App\Repositories\LaundryOrderRepository;
use App\Models\Customer;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class LaundryOrderService
{
    public const EXPRESS_SERVICE_FEE = 150.00;

    protected $orderRepository;

    public function __construct(LaundryOrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function processNewOrder(array $validatedData)
    {
        return DB::transaction(function () use ($validatedData) {
            $phone = $this->formatPhone($validatedData['contact_number']);

            if (strlen($phone) < 10 || strlen($phone) > 11) {
                throw ValidationException::withMessages([
                    'contact_number' => 'Enter a valid contact number using 10–11 digits.',
                ]);
            }

            $customer = Customer::firstOrCreate(
                ['phone' => $phone],
                ['name' => trim($validatedData['customer_name']), 'address' => 'N/A']
            );

            $expressService = (bool) ($validatedData['express_service'] ?? false);
            $expressFee = $expressService ? self::EXPRESS_SERVICE_FEE : 0;
            $subtotal = 0;
            $detailsData = [];

            foreach ($validatedData['items'] as $item) {
                $service = ServiceType::findOrFail($item['service_type_id']);
                $lineSubtotal = round((float) $service->price_per_kg * (float) $item['weight_kg'], 2);
                $subtotal += $lineSubtotal;

                $detailsData[] = [
                    'service_type_id' => $service->id,
                    'weight' => $item['weight_kg'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $grandTotal = round($subtotal + $expressFee, 2);
            $order = $this->orderRepository->createOrder([
                'customer_id' => $customer->id,
                'status' => 'pending',
                'pickup_date' => $validatedData['pickup_date'],
                'express_service' => $expressService,
                'express_fee' => $expressFee,
                'total_amount' => $grandTotal,
            ], $detailsData);

            $deliveryDate = Carbon::parse($validatedData['pickup_date'])
                ->addDays($expressService ? 1 : 2)
                ->toDateString();

            $order->delivery()->create([
                'delivery_date' => $deliveryDate,
                'delivery_status' => 'unassigned',
            ]);

            return $order->load(['customer', 'orderDetails.serviceType', 'delivery']);
        });
    }

    private function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (str_starts_with($digits, '63') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }

        return $digits;
    }
}