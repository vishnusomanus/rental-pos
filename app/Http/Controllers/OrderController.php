<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request) {
        $orders = new Order();
        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->with(['items', 'payments', 'customer'])->latest()->paginate(10);

        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function pending(Request $request) {
        $orders = new Order();
        
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        
        $orders = Order::query()
        ->join(DB::raw('(SELECT order_id, SUM(price) AS total_price FROM order_items GROUP BY order_id) AS oi'), 'orders.id', '=', 'oi.order_id')
        ->join(DB::raw('(SELECT order_id, SUM(amount) AS total_paid FROM payments GROUP BY order_id) AS p'), 'orders.id', '=', 'p.order_id')
        ->select('orders.*', 'oi.total_price', 'p.total_paid')
        ->whereRaw('p.total_paid = 0 OR p.total_paid < oi.total_price')
        ->paginate(10);


    
        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();
    
        return view('orders.pending', compact('orders', 'total', 'receivedAmount'));
    }
    

    public function store(OrderStoreRequest $request)
    {
        $order = Order::create([
            'customer_id' => $request->customer_id,
            'proof' => $request->customer_proof,
            'notes' => $request->customer_notes,
            'user_id' => $request->user()->id,
        ]);

        $cart = $request->user()->cart()->get();
        foreach ($cart as $item) {
            $order->items()->create([
                'price' => $item->price * $item->pivot->quantity * $item->pivot->days,
                'quantity' => $item->pivot->quantity,
                'days' => $item->pivot->days,
                'product_id' => $item->id,
            ]);
            $item->quantity = $item->quantity - $item->pivot->quantity;
            $item->save();
        }
        $request->user()->cart()->detach();
        $order->payments()->create([
            'amount' => $request->amount,
            'user_id' => $request->user()->id,
        ]);
        return 'success';
    }
}
