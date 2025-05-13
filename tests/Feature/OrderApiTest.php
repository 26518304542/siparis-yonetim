<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_an_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->postJson('/api/orders', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'status' => 'pending',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment([
                    'status' => 'pending',
                    'quantity' => 1,
                    'product_id' => $product->id,
                    'customer_id' => $customer->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_lists_orders()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        Order::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'status' => 'pending'
        ]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                    'customer_id' => $customer->id,
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'status' => 'pending'
                 ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_an_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'status' => 'pending'
        ]);

        $response = $this->putJson("/api/orders/{$order->id}", [
            'status' => 'completed'
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'completed']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_an_order()
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'product_id' => $product->id,
        ]);

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('orders', ['id' => $order->id]);
    }
}
