<?php

namespace Tests\Feature;

use App\Models\ServiceType;
use App\Models\LaundryOrder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaundryOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_form_loads_available_service_types(): void
    {
        ServiceType::create([
            'name' => 'Dry Clean',
            'price_per_kg' => 120,
        ]);

        $this->get('/create-order')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('laundry/create')
                ->has('serviceTypes', 1)
                ->where('serviceTypes.0.name', 'Dry Clean'));
    }

    public function test_order_submission_saves_customer_order_and_selected_service(): void
    {
        $service = ServiceType::create([
            'name' => '  Dry Clean  ',
            'price_per_kg' => 120,
        ]);

        $pickupDate = now()->addDay()->toDateString();

        $this->from('/create-order')->post('/orders', [
            'customer_name' => 'Test Customer',
            'contact_number' => '+63 (993) 606-3268',
            'pickup_date' => $pickupDate,
            'items' => [
                [
                    'service_type_id' => (string) $service->id,
                    'weight_kg' => '5',
                ],
            ],
        ])->assertRedirect('/create-order')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'name' => 'Test Customer',
            'phone' => '9936063268',
        ]);

        $this->assertDatabaseHas('service_types', [
            'id' => $service->id,
            'name' => 'Dry Clean',
        ]);

        $this->assertDatabaseHas('laundry_orders', [
            'status' => 'pending',
            'pickup_date' => Carbon::parse($pickupDate)->startOfDay()->toDateTimeString(),
            'express_service' => false,
            'express_fee' => 0,
            'total_amount' => 600,
        ]);
        $this->assertSame('600.00', LaundryOrder::firstOrFail()->grand_total);

        $this->assertDatabaseHas('order_details', [
            'service_type_id' => $service->id,
            'weight' => 5,
            'subtotal' => 600,
        ]);

        $this->assertDatabaseHas('deliveries', [
            'delivery_date' => Carbon::parse($pickupDate)->addDays(2)->startOfDay()->toDateTimeString(),
            'delivery_status' => 'unassigned',
        ]);
    }

    public function test_express_orders_add_the_fee_and_use_the_express_delivery_date(): void
    {
        $service = ServiceType::create([
            'name' => 'Wash & Fold',
            'price_per_kg' => 50,
        ]);
        $pickupDate = now()->addDay()->toDateString();

        $this->from('/create-order')->post('/orders', [
            'customer_name' => 'Express Customer',
            'contact_number' => '09936063268',
            'pickup_date' => $pickupDate,
            'express_service' => true,
            'items' => [
                [
                    'service_type_id' => (string) $service->id,
                    'weight_kg' => '2',
                ],
            ],
        ])->assertRedirect('/create-order')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('laundry_orders', [
            'express_service' => true,
            'express_fee' => 150,
            'total_amount' => 250,
        ]);

        $this->assertDatabaseHas('deliveries', [
            'delivery_date' => Carbon::parse($pickupDate)->addDay()->startOfDay()->toDateTimeString(),
        ]);
    }

    public function test_guests_can_open_login_and_registration_pages(): void
    {
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }
}
