<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Payment;
use App\Models\OrderItem;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $orders = Order::forUser($user)->with(['items', 'payments'])->get();
        $customers_count = Customer::forUser($user)->count();


        $weeklyStatistics = [];
    
        // Get the start and end date of the current week
        $startDate = Carbon::now()->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();
        
        // Loop through each day of the week
        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $dayOfWeek = $date->format('l');
            
            // Get the total rented amount for the day from the Payment model
            $rentedAmount = Payment::forUser(auth()->user())
                ->whereDate('created_at', $date)
                ->sum('amount');
            
            // Get the total number of products on rent for the day from the OrderItem model
            $productOnRent = OrderItem::whereDate('created_at', $date)
                ->sum('quantity');
            
            // Store the daily statistics in the result array
            $weeklyStatistics[$dayOfWeek] = [
                'rented_amount' => $rentedAmount,
                'product_on_rent' => $productOnRent,
            ];
        }


        $mostSellingProducts = Product::mostSelling($user)->get();

        $topCustomers = Customer::topCustomers($user)->get();

        return view('home', [
            'orders_count' => $orders->count(),
            'income' => $orders->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'income_today' => $orders->where('created_at', '>=', date('Y-m-d').' 00:00:00')->map(function($i) {
                if($i->receivedAmount() > $i->total()) {
                    return $i->total();
                }
                return $i->receivedAmount();
            })->sum(),
            'customers_count' => $customers_count,
            'weeklyStatistics' => $weeklyStatistics,
            'mostSellingProducts' => $mostSellingProducts,
            'topCustomers' => $topCustomers,
        ]);
    }
}
