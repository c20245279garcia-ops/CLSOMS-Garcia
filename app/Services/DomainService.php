<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\LaundryOrder;
use App\Models\OrderDetail;
use App\Models\Delivery;
use App\Models\ServiceType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DomainService
{
    public function sanitizeString(?string $value): string
    {
        $clean = strip_tags((string) $value);
        return trim(preg_replace('/\s+/', ' ', $clean));
    }

    public function formatPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (str_starts_with($digits, '0')) {
            $digits = substr($digits, 1);
        }
        return $digits;
    }

    public function formatMoney($amount): float
    {
        return round((float) $amount, 2);
    }

    public function calculateGrandTotal(array $items, bool $expressService): array
    {
        $lines = [];
        $sum = 0;

        foreach ($items as $item) {
            $service = ServiceType::findOrFail($item['service_type_id']);
            $price   = $this->formatMoney($service->price_per_kg);
            $weight  = $this->formatMoney($item['weight_kg']);
            $line    = $this->formatMoney($price * $weight);

            $lines[] = [
                'service_type_id' => $service->service_type_id,
                'price_per_kg'    => $price,
                'weight_kg'       => $weight,
                'line_total'      => $line,
            ];
            $sum += $line;
        }

        $expressFee = $expressService ? 150.00 : 0.00;
        $grandTotal = $this->formatMoney($sum + $expressFee);

        return [
            'lines'       => $lines,
            'express_fee' => $expressFee,
            'grand_total' => $grandTotal,
        ];
    }

    public function calculateDeliveryDate(string $pickupDate, bool $expressService): string
    {
        $pickup = Carbon::parse($pickupDate);
        return $expressService
            ? $pickup->copy()->addDay()->toDateString()
            : $pickup->copy()->addDays(2)->toDateString();
    }

    public function createLaundryOrder(array $data): LaundryOrder
    {
        return DB::transaction(function () use ($data) {
            $customer = Customer::firstOrCreate(
                ['contact_number' => $this->formatPhone($data['contact_number'])],
                ['customer_name'  => $this->sanitizeString($data['customer_name'])]
            );

            $express = (bool) ($data['express_service'] ?? false);
            $calc    = $this->calculateGrandTotal($data['items'], $express);

            $order = LaundryOrder::create([
                'customer_id'    => $customer->customer_id,
                'pickup_date'    => $data['pickup_date'],
                'laundry_status' => 'Pending',
                'grand_total'    => $calc['grand_total'],
                'payment_status' => $data['payment_status'] ?? 'Unpaid',
            ]);

            foreach ($calc['lines'] as $line) {
                OrderDetail::create([
                    'order_id'        => $order->order_id,
                    'service_type_id' => $line['service_type_id'],
                    'price_per_kg'    => $line['price_per_kg'],
                    'weight_kg'       => $line['weight_kg'],
                    'express_fee'     => $express ? $calc['express_fee'] / count($calc['lines']) : 0,
                ]);
            }

            Delivery::create([
                'order_id'      => $order->order_id,
                'delivery_date' => $this->calculateDeliveryDate($data['pickup_date'], $express),
            ]);

            return $order->load('customer', 'orderDetails.serviceType', 'delivery');
        });
    }

    public function updateLaundryOrder(int $orderId, array $data): LaundryOrder
    {
        return DB::transaction(function () use ($orderId, $data) {
            $order = LaundryOrder::findOrFail($orderId);
            $payload = [];
            if (isset($data['laundry_status'])) $payload['laundry_status'] = $data['laundry_status'];
            if (isset($data['payment_status'])) $payload['payment_status'] = $data['payment_status'];
            if (isset($data['pickup_date']))    $payload['pickup_date'] = $data['pickup_date'];

            $order->update($payload);
            return $order->fresh()->load('customer', 'orderDetails.serviceType', 'delivery');
        });
    }

    public function createDelivery(array $data): Delivery
    {
        return DB::transaction(function () use ($data) {
            $order = LaundryOrder::findOrFail($data['laundry_order_id']);
            if (empty($data['delivery_date']) && !$order->pickup_date) {
                throw ValidationException::withMessages([
                    'laundry_order_id' => 'The order needs a pickup date before a delivery can be scheduled.',
                ]);
            }

            $deliveryDate = $data['delivery_date'] ?? $this->calculateDeliveryDate(
                $order->pickup_date->toDateString(),
                $order->express_service
            );

            return $order->delivery()->create([
                'delivery_date' => $deliveryDate,
                'delivery_status' => 'unassigned',
            ]);
        });
    }

    public function updateDelivery(int $deliveryId, array $data): Delivery
    {
        return DB::transaction(function () use ($deliveryId, $data) {
            $delivery = Delivery::findOrFail($deliveryId);
            $delivery->update(['delivery_date' => $data['delivery_date']]);
            return $delivery->fresh();
        });
    }
}