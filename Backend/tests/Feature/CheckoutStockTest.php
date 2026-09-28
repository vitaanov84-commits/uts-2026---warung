<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_checkout_reduces_product_stock(): void
    {
        $product = Product::create([
            'name' => 'Es Teh',
            'price' => 5000,
            'stock' => 10,
        ]);

        $response = $this->postJson('/api/checkout', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
            'payment_method' => 'bca',
            'payment_status' => 'success',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 7,
        ]);
    }

    public function test_failed_checkout_does_not_reduce_product_stock(): void
    {
        $product = Product::create([
            'name' => 'Kopi',
            'price' => 7000,
            'stock' => 10,
        ]);

        $response = $this->postJson('/api/checkout', [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
            'payment_method' => 'dana',
            'payment_status' => 'failed',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
        ]);
    }
}
