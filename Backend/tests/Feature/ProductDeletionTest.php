<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_deleted_after_order_history_exists(): void
    {
        $product = Product::create([
            'name' => 'Teh Botol',
            'price' => 5000,
            'stock' => 20,
        ]);

        $order = Order::create([
            'total' => 5000,
            'status' => 'success',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => 5000,
            'subtotal' => 5000,
        ]);

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertOk();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Teh Botol',
        ]);
    }
}
