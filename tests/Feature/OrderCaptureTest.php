<?php

namespace Tests\Feature;

use App\Mail\OrderCreated;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderCaptureTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_captures_a_verified_paypal_order_and_marks_the_artwork_as_sold(): void
    {
        config()->set([
            'services.paypal.client_id' => 'client-id',
            'services.paypal.client_secret' => 'client-secret',
            'services.paypal.base_url' => 'https://paypal.test',
            'services.paypal.currency' => 'EUR',
            'services.orders.admin_email' => 'admin@example.com',
        ]);

        $artwork = Artwork::factory()->create([
            'price' => 125.50,
            'is_for_sale' => true,
            'status' => 'available',
        ]);

        Http::fake([
            'https://paypal.test/v1/oauth2/token' => Http::response([
                'access_token' => 'access-token',
            ]),
            'https://paypal.test/v2/checkout/orders/PAYPAL-123/capture' => Http::response([
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => '125.50',
                    ],
                ]],
            ]),
        ]);
        Mail::fake();

        $response = $this->postJson('/api/orders/capture', [
            'artwork_id' => $artwork->id,
            'paypal_order_id' => 'PAYPAL-123',
            'customer_name' => 'Ada Lovelace',
            'customer_email' => 'ada@example.com',
            'shipping_address' => [
                'address_line_1' => '1 rue de Paris',
                'admin_area_2' => 'Paris',
                'postal_code' => '75001',
                'country_code' => 'FR',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('status', 'paid')
            ->assertJsonPath('artwork.id', $artwork->id);

        $this->assertDatabaseHas('orders', [
            'paypal_order_id' => 'PAYPAL-123',
            'artwork_id' => $artwork->id,
            'status' => 'paid',
            'amount' => '125.50',
        ]);
        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'status' => 'sold',
        ]);
        Mail::assertSent(OrderCreated::class);
    }

    public function test_it_rejects_a_paypal_amount_that_does_not_match_the_artwork_price(): void
    {
        config()->set([
            'services.paypal.client_id' => 'client-id',
            'services.paypal.client_secret' => 'client-secret',
            'services.paypal.base_url' => 'https://paypal.test',
            'services.paypal.currency' => 'EUR',
        ]);

        $artwork = Artwork::factory()->create([
            'price' => 125.50,
            'is_for_sale' => true,
            'status' => 'available',
        ]);

        Http::fake([
            'https://paypal.test/v1/oauth2/token' => Http::response(['access_token' => 'access-token']),
            'https://paypal.test/v2/checkout/orders/PAYPAL-456/capture' => Http::response([
                'status' => 'COMPLETED',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value' => '1.00',
                    ],
                ]],
            ]),
        ]);

        $response = $this->postJson('/api/orders/capture', [
            'artwork_id' => $artwork->id,
            'paypal_order_id' => 'PAYPAL-456',
            'customer_name' => 'Ada Lovelace',
            'customer_email' => 'ada@example.com',
            'shipping_address' => [
                'address_line_1' => '1 rue de Paris',
                'admin_area_2' => 'Paris',
                'postal_code' => '75001',
                'country_code' => 'FR',
            ],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'status' => 'available',
        ]);
    }
}
