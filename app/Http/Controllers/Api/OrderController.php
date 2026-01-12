<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;


class OrderController extends Controller
{
    public function index()
    {
        return response()->json([
            'status' => true,
            'data' => Order::with('user')->get()
        ]);
    }
public function store(Request $request)
{
    $request->validate([
        'borrow_date' => 'required|date'
    ]);

    $order = Order::create([
        'user_id'     => $request->user()->id,
        'borrow_date' => $request->borrow_date,
        'status'      => 'dipinjam'
    ]);

    ActivityLog::create([
        'user_id'     => $request->user()->id,
        'action'      => 'CREATE_ORDER',
        'description' => 'Membuat order ID ' . $order->id
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Order berhasil dibuat',
        'data'    => $order
    ], 201);
}


    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }

    // 🔁 KEMBALIKAN (ADMIN)
    public function update($id)
    {
        $order = Order::find($id);

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak ditemukan'
            ], 404);
        }

        $order->update([
            'status' => 'dikembalikan',
            'return_date' => now()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Order dikembalikan'
        ]);
    }

    // 📦 ORDER USER LOGIN
    public function myOrders(Request $request)
    {
        $orders = Order::with(['items.product'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    // ✅ CHECKOUT / SELESAIKAN ORDER
public function checkout($id)
{
    DB::beginTransaction();

    try {
        $order = Order::where('id', $id)
            ->where('status', 'dipinjam')
            ->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order tidak valid atau sudah selesai'
            ], 400);
        }

        $order->update([
            'status'      => 'selesai',
            'return_date' => now()
        ]);

        ActivityLog::create([
            'user_id'     => $order->user_id,
            'action'      => 'CHECKOUT_ORDER',
            'description' => 'Checkout order ID ' . $order->id
        ]);

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Checkout berhasil'
        ]);

    } catch (\Exception $e) {
    DB::rollBack();

    return response()->json([
        'status' => false,
        'message' => 'Checkout gagal',
        'error'   => $e->getMessage()
    ], 500);}

}
    

    public function destroy($id)
{
    $order = Order::find($id);

    if (!$order) {
        return response()->json([
            'status' => false,
            'message' => 'Order tidak ditemukan'
        ], 404);
    }

    $order->delete();

    return response()->json([
        'status' => true,
        'message' => 'Order berhasil dihapus'
    ]);

}
}



