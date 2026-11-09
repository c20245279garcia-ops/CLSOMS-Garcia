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
                ->component('laundry/Create')
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

    public function test_new_order_updates_the_name_shown_for_all_orders_with_the_same_phone(): void
    {
        $service = ServiceType::create([
            'name' => 'Wash & Fold',
            'price_per_kg' => 50,
        ]);
        $order = [
            'contact_number' => '09936063268',
            'pickup_date' => now()->addDay()->toDateString(),
            'items' => [
                [
                    'service_type_id' => (string) $service->id,
                    'weight_kg' => '2',
                ],
            ],
        ];

        $this->post('/orders', ['customer_name' => 'First Name'] + $order)->assertRedirect();
        $this->travel(1)->seconds();
        $this->post('/orders', ['customer_name' => '  uPDATEd   nAME  '] + $order)->assertRedirect();

        $this->assertDatabaseCount('customers', 1);
        $this->assertDatabaseHas('customers', [
            'phone' => '9936063268',
            'name' => 'Updated Name',
        ]);

        $this->get('/orders-list')
            ->assertInertia(fn ($page) => $page
                ->component('laundry/Index')
                ->has('orders', 2)
                ->where('orders.0.customer.name', 'Updated Name')
                ->where('orders.1.customer.name', 'Updated Name'));
    }

    public function test_order_amount_above_database_limit_is_rejected_with_a_validation_message(): void
    {
        $service = ServiceType::create([
            'name' => 'Dry Clean',
            'price_per_kg' => 120,
        ]);

        $this->from('/create-order')->post('/orders', [
            'customer_name' => 'Test Customer',
            'contact_number' => '09936063268',
            'pickup_date' => now()->addDay()->toDateString(),
            'items' => [
                [
                    'service_type_id' => (string) $service->id,
                    'weight_kg' => '900000',
                ],
            ],
        ])->assertRedirect('/create-order')
            ->assertSessionHasErrors([
                'items' => 'This order amount is too high to be valid. Please enter a realistic laundry weight.',
            ]);

        $this->assertDatabaseCount('laundry_orders', 0);
    }

    public function test_impossibly_large_weight_is_rejected_before_order_creation(): void
    {
        $service = ServiceType::create([
            'name' => 'Dry Clean',
            'price_per_kg' => 120,
        ]);

        $this->from('/create-order')->post('/orders', [
            'customer_name' => 'Test Customer',
            'contact_number' => '09936063268',
            'pickup_date' => now()->addDay()->toDateString(),
            'items' => [
                [
                    'service_type_id' => (string) $service->id,
                    'weight_kg' => '3.6e47',
                ],
            ],
        ])->assertRedirect('/create-order')
            ->assertSessionHasErrors([
                'items.0.weight_kg' => 'That weight is too large to be a valid laundry order.',
            ]);

        $this->assertDatabaseCount('laundry_orders', 0);
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
