<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pemasukan;
use App\Models\Product;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected function withCodes($order)
    {
        $order->order_code = 'WS-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
        $order->customer_code = 'CUST-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);

        if ($order->relationLoaded('items')) {
            foreach ($order->items as $item) {
                $item->subtotal = $item->subtotal ?? (float) $item->unit_price * (int) $item->quantity;
            }
        }

        if ($order->relationLoaded('payment')) {
            $order->payment->reference = $order->payment->reference ?? 'PAY-'.strtoupper(Str::random(10));
        }

        return $order;
    }

    public function index()
    {
        $orders = Order::with(['items', 'payment'])->latest()->get()->map(function ($order) {
            return $this->withCodes($order);
        });

        return response()->json($orders);
    }

    public function store(Request $request, DatabaseManager $database)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string|in:bca,dana,gopay',
            'payment_status' => 'sometimes|string|in:success,failed',
        ]);

        try {
            $order = $database->transaction(function () use ($validated) {
                $products = Product::whereIn('id', collect($validated['items'])->pluck('product_id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $lines = [];
                $total = 0;

                foreach ($validated['items'] as $item) {
                    $product = $products->get($item['product_id']);
                    if (!$product || $product->stock < $item['quantity']) {
                        abort(422, 'Stok produk tidak mencukupi: '.($product?->name ?? 'produk tidak ditemukan'));
                    }

                    $subtotal = (float) $product->price * $item['quantity'];
                    $total += $subtotal;
                    $lines[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ];
                }

                $paymentStatus = $validated['payment_status'] ?? 'success';
                $failed = $paymentStatus === 'failed';
                $order = Order::create([
                    'total' => $total,
                    'status' => $failed ? 'failed' : 'success',
                    'failure_reason' => $failed ? 'Pembayaran ditolak oleh kasir.' : null,
                ]);

                foreach ($lines as $line) {
                    $order->items()->create([
                        'product_id' => $line['product']->id,
                        'product_name' => $line['product']->name,
                        'quantity' => $line['quantity'],
                        'unit_price' => $line['unit_price'],
                        'subtotal' => $line['subtotal'],
                    ]);

                    if (!$failed) {
                        $line['product']->decrement('stock', $line['quantity']);
                    }
                }

                $order->payment()->create([
                    'method' => $validated['payment_method'],
                    'amount' => $total,
                    'status' => $paymentStatus,
                    'reference' => $failed ? null : 'PAY-'.Str::upper(Str::random(10)),
                    'failure_reason' => $failed ? 'Pembayaran ditolak oleh kasir.' : null,
                ]);

                if (!$failed) {
                    Pemasukan::create([
                        'total' => $total,
                        'source' => 'checkout',
                        'note' => 'Order #'.$order->id,
                    ]);
                }

                return $order->load(['items', 'payment']);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            throw $exception;
        }

        $order->load(['items', 'payment']);

        return response()->json([
            'message' => $order->status === 'success' ? 'Pembayaran berhasil' : 'Pembayaran gagal',
            'data' => $this->withCodes($order),
        ], $order->status === 'success' ? 201 : 422);
    }
}
