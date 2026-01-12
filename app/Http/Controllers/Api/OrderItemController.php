<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ActivityLog;

class OrderItemController extends Controller
{
    // 1️⃣ LIST ORDER ITEMS (punya user login)
    public function index(Request $request)
    {
        $items = OrderItem::whereHas('order', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with(['product', 'order'])->get();

        return response()->json([
            'status' => true,
            'data' => $items
        ]);
    }

    // 2️⃣ DETAIL ORDER ITEM
    public function show(Request $request, $id)
    {
        $item = OrderItem::with(['product', 'order'])->findOrFail($id);

        if ($item->order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        return response()->json([
            'status' => true,
            'data' => $item
        ]);
    }

    // 3️⃣ CREATE ORDER ITEM
    public function store(Request $request)
    {
        $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $product = Product::findOrFail($request->product_id);

        if ($request->quantity > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => 'Stok tidak mencukupi'
            ], 422);
        }

        $item = OrderItem::where('order_id', $order->id)
            ->where('product_id', $product->id)
            ->first();

        if ($item) {
            $item->increment('quantity', $request->quantity);
        } else {
            $item = OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'price'      => $product->price,
                'quantity'   => $request->quantity,
            ]);
        }

        $product->decrement('stock', $request->quantity);
        $order->calculateTotal();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'ADD_ORDER_ITEM',
            'description' => 'Tambah item ke order ID ' . $order->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order item berhasil ditambahkan',
            'data' => $item
        ]);
    }

    // 4️⃣ UPDATE ORDER ITEM (UBAH QTY)
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $item = OrderItem::findOrFail($id);
        $order = $item->order;

        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $diff = $request->quantity - $item->quantity;
        $product = $item->product;

        if ($diff > 0 && $diff > $product->stock) {
            return response()->json([
                'status' => false,
                'message' => 'Stok tidak mencukupi'
            ], 422);
        }

        $item->update(['quantity' => $request->quantity]);
        $product->decrement('stock', $diff);
        $order->calculateTotal();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'UPDATE_ORDER_ITEM',
            'description' => 'Update order item ID ' . $item->id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order item berhasil diupdate',
            'data' => $item
        ]);
    }

    // 5️⃣ DELETE ORDER ITEM
    public function destroy(Request $request, $id)
    {
        $item = OrderItem::findOrFail($id);
        $order = $item->order;

        if ($order->user_id !== $request->user()->id) {
            return response()->json([
                'status' => false,
                'message' => 'Akses ditolak'
            ], 403);
        }

        $item->product->increment('stock', $item->quantity);
        $item->delete();

        $order->calculateTotal();

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'DELETE_ORDER_ITEM',
            'description' => 'Hapus order item ID ' . $id
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order item berhasil dihapus'
        ]);
    }
}
