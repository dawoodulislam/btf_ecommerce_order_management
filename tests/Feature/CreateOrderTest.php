<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_create_order_and_inventory_decrements()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $variant = ProductVariant::factory()->create();
        $variant->inventory()->create(['quantity'=>10]);

        $payload = [
            'items' => [
                ['variant_id' => $variant->id, 'quantity' => 2]
            ],
            'shipping' => 5
        ];

        $resp = $this->postJson('/api/v1/orders', $payload);
        $resp->assertStatus(201)
             ->assertJsonPath('subtotal',  ($variant->price * 2));

        $this->assertDatabaseHas('inventories', ['product_variant_id' => $variant->id, 'quantity' => 8]);
    }
}
