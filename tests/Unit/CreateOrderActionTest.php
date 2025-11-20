<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Actions\CreateOrderAction;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_execute_creates_order()
    {
        $variant = ProductVariant::factory()->create();
        $variant->inventory()->create(['quantity' => 5]);

        $user = \App\Models\User::factory()->create();

        $action = new CreateOrderAction();
        $order = $action->execute(['items'=>[['variant_id'=>$variant->id,'quantity'=>1]]], $user);
        $this->assertNotNull($order->id);
        $this->assertEquals(1, $order->items()->count());
    }
}
