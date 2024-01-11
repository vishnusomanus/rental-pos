<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    public function index(Request $request) {
        $user = auth()->user();
        $orders = new Order();
        if($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        if($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        $orders = $orders->forUser($user)->with(['items', 'payments', 'customer'])->latest()->paginate(config('settings.pagination'));

        $total = $orders->map(function($i) {
            return $i->total();
        })->sum();
        $receivedAmount = $orders->map(function($i) {
            return $i->receivedAmount();
        })->sum();

        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function pending(Request $request) {
        $user = auth()->user();
        $orders = new Order();
        
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
        
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        
        $orders = Order::forUser($user)
        // ->query()
        ->join(DB::raw('(SELECT order_id, SUM(price) AS total_price FROM order_items GROUP BY order_id) AS oi'), 'orders.id', '=', 'oi.order_id')
        ->join(DB::raw('(SELECT order_id, SUM(amount) AS total_paid FROM payments GROUP BY order_id) AS p'), 'orders.id', '=', 'p.order_id')
        ->select('orders.*', 'oi.total_price', 'p.total_paid')
        ->whereRaw('p.total_paid = 0 OR p.total_paid < oi.total_price')
        ->paginate(config('settings.pagination'));


    
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
            'white_label_id' => $request->user()->white_label_id
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
            'white_label_id' => $request->user()->white_label_id
        ]);
        return 'success';
    }
    public function edit(Order $order)
    {
        $order->load('customer', 'items.product');
        return view('orders.edit', compact('order'));
    }
    public function invoice( Request $request)
    {
        $order = Order::findOrFail($request->id);
        $order->load('customer', 'items.product');
        $imagePath = public_path('images/logo.png');
        $base64Image = base64_encode(file_get_contents($imagePath));
        //return view('orders.invoice', compact('order', 'base64Image'));
        
        if ($request->headers->get('referer') === url('/admin/orders')) {
            $dompdf = new Dompdf();
            
            $html = view('orders.invoice', compact('order', 'base64Image'))->render();
            
            $dompdf->loadHtml($html);
            $dompdf->render();
            $output = $dompdf->output();
            return Response::make($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice.pdf"',
            ]);
        } else {
            return "No access!";
        }

    }

    public function update(Request $request, Order $order)
    {
        $payment = Payment::create([
            'amount' => $request->balance,
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'white_label_id' => $request->user()->white_label_id
        ]);
    
        if (!$payment) {
            return redirect()->back()->with('error', "error occured");
        }
        return redirect()->route('orders.pending')->with('success', "Order updated!");
    }
}
