<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderStoreRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Response;
use League\Csv\Writer;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->getFilteredOrders()->with(['items', 'payments', 'customer'])
        ->latest()->paginate(config('settings.pagination'))
        ->appends(request()->except('page'));
    
        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
    
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();
    
        return view('orders.index', compact('orders', 'total', 'receivedAmount'));
    }

    public function pending(Request $request)
    {
        $user = auth()->user();
        $orders = new Order();
    
        if ($request->start_date) {
            $orders = $orders->where('created_at', '>=', $request->start_date);
        }
    
        if ($request->end_date) {
            $orders = $orders->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }
        
        if ($request->search) {
            $orders = $orders->whereHas('customer', function ($query) use ($request) {
                $query->where(function ($innerQuery) use ($request) {
                    $innerQuery->where('first_name', 'LIKE', "%{$request->search}%")
                        ->orWhere('last_name', 'LIKE', "%{$request->search}%");
                });
            });
        }
    
        $orders = Order::query()
            ->join(DB::raw('(SELECT order_id, SUM(price) AS total_price FROM order_items GROUP BY order_id) AS oi'), 'orders.id', '=', 'oi.order_id')
            ->join(DB::raw('(SELECT order_id, SUM(amount) AS total_paid FROM payments GROUP BY order_id) AS p'), 'orders.id', '=', 'p.order_id')
            ->select('orders.*', 'oi.total_price', 'p.total_paid')
            ->whereRaw('p.total_paid = 0 OR p.total_paid < oi.total_price')
            ->forUser($user)
            ->with(['items', 'payments', 'customer'])
            ->paginate(config('settings.pagination'))
            ->appends(request()->except('page'));
    
        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
    
        $receivedAmount = $orders->map(function ($i) {
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
        $id = $request->id;
        $order = Order::findOrFail($id);
        $order->load('customer', 'items.product');
        $imagePath = public_path('images/logo.png');
        $base64Image = base64_encode(file_get_contents($imagePath));
        //return view('orders.invoice', compact('id', 'order', 'base64Image'));
        
        if (strpos($request->headers->get('referer'), '/admin/orders') !== false) {
            $dompdf = new Dompdf();
            
            $html = view('orders.invoice', compact('id', 'order', 'base64Image'))->render();
            
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

    public function exportPDF()
    {
        $orders = $this->getFilteredOrders()->with(['items', 'payments', 'customer'])
        ->get();

        $total = $orders->map(function ($i) {
            return $i->total();
        })->sum();
    
        $receivedAmount = $orders->map(function ($i) {
            return $i->receivedAmount();
        })->sum();
        
        $html = view('orders.export', compact('orders', 'total', 'receivedAmount'))->render();
    
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $fileName = 'orders_' . date('Y-m-d') . '.pdf';
        return $dompdf->stream($fileName);
    }
    
    public function exportCSV()
    {
        $orders = $this->getFilteredOrders()->with(['items', 'payments', 'customer'])->get();
        
        $csv = Writer::createFromString('');
    
        $csv->insertOne([
            __('order.Customer_Name'),
            __('order.Total'),
            __('order.Received_Amount'),
            // __('order.Status'),
            __('order.To_Pay'),
            __('order.Created_At')
        ]);
    
        foreach ($orders as $order) {
            $csv->insertOne([
                $order->getCustomerName(),
                $order->formattedTotal(),
                $order->formattedReceivedAmount(),
                // $order->getStatus(),
                number_format($order->balance(), 2),
                $order->created_at
            ]);
        }
        $fileName = 'orders_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv->getContent();
        }, $fileName, $headers);
    }

    private function getFilteredOrders()
    {
        $user = auth()->user();
        $orders = Order::forUser($user);
    
        if (request()->has('start_date')) {
            $orders = $orders->where('created_at', '>=', request('start_date'));
        }
    
        if (request()->has('end_date')) {
            $orders = $orders->where('created_at', '<=', request('end_date') . ' 23:59:59');
        }
            
        if (request()->has('search')) {
            $search = request('search');
            $orders = $orders->whereHas('customer', function ($query) use ($search) {
                $query->where('first_name', 'LIKE', "%{$search}%")
                    ->orWhere('last_name', 'LIKE', "%{$search}%");
            });
        }
    
        return $orders;
    }
    
    
}
