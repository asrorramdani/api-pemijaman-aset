<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * 📋 ADMIN - Get all orders
     */
    public function index()
    {
        $orders = Order::with('user')->get();

        return response()->json([
            'status' => true,
            'data'   => $orders,
        ]);
    }

    /**
     * ➕ USER - Create order
     */
    public function store(Request $request)
    {
        $request->validate([
            'borrow_date' => 'required|date',
        ]);

        $order = Order::create([
            'user_id'     => $request->user()->id,
            'borrow_date' => $request->borrow_date,
            'status'      => 'dipinjam',
        ]);

        ActivityLog::create([
            'user_id'     => $request->user()->id,
            'action'      => 'CREATE_ORDER',
            'description' => 'Membuat order ID ' . $order->id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Order berhasil dibuat',
            'data'    => $order,
        ], 201);
    }

    /**
     * 🔍 ADMIN - Detail order
     */
    public function show(int $id)
    {
        $order = Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data'   => $order,
        ]);
    }

    /**
     * 🔁 ADMIN - Pengembalian order
     */
    public function update(int $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        $order->update([
            'status'      => 'dikembalikan',
            'return_date' => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Order dikembalikan',
        ]);
    }

    /**
     * 📦 USER - Orders milik user login
     */
    public function myOrders(Request $request)
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $orders,
        ]);
    }

    /**
     * ✅ Checkout order
     */
    public function checkout(int $id)
    {
        DB::beginTransaction();

        try {
            $order = Order::where('id', $id)
                ->where('status', 'dipinjam')
                ->first();

            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Order tidak valid atau sudah selesai',
                ], 400);
            }

            $order->update([
                'status'      => 'selesai',
                'return_date' => now(),
            ]);

            ActivityLog::create([
                'user_id'     => $order->user_id,
                'action'      => 'CHECKOUT_ORDER',
                'description' => 'Checkout order ID ' . $order->id,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Checkout berhasil',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Checkout gagal',
            ], 500);
        }
    }

    /**
     * ❌ ADMIN - Delete order
     */
    public function destroy(int $id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status'  => false,
                'message' => 'Order tidak ditemukan',
            ], 404);
        }

        $order->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Order berhasil dihapus',
        ]);
    }
}