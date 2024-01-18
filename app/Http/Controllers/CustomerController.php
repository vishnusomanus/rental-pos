<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (request()->wantsJson()) {
            return response(
                Customer::forUser($user)->get()
            );
        }

        $customers = Customer::forUser($user);
        if ($request->search) {
            $customers = $customers->where(function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->search}%");
            });
        }
        
        $paginationConfig = config('settings.pagination');
        $customers = $customers->latest()->paginate($paginationConfig)->appends(request()->except('page'));
        return view('customers.index')->with('customers', $customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = ''; 

        if ($request->hasFile('avatar')) {
            $avatar_path = $request->file('avatar')->store('customers', 'public');
        }

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatar_path,
            'user_id' => $request->user()->id,
            'white_label_id' => $request->user()->white_label_id
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', __('customer.error_creating'));
        }
        return redirect()->route('customers.index')->with('success', __('customer.succes_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($customer->avatar) {
                Storage::delete($customer->avatar);
            }
            // Store avatar
            $avatar_path = $request->file('avatar')->store('customers', 'public');
            // Save to Database
            $customer->avatar = $avatar_path;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', __('customer.error_updating'));
        }
        return redirect()->route('customers.index')->with('success', __('customer.success_updating'));
    }

    public function destroy(Customer $customer)
    {
        if ($customer->avatar) {
            Storage::delete($customer->avatar);
        }

        $customer->delete();

       return response()->json([
           'success' => true
       ]);
    }

    public function exportCsv()
    {
        $customers = $this->getFilteredCustomers();
        
        $csv = Writer::createFromString('');
        $csv->insertOne(['First Name', 'Last Name', 'Email', 'Phone', 'Address']);
        
        foreach ($customers as $customer) {
            $csv->insertOne([$customer->first_name, $customer->last_name, $customer->email, $customer->phone, $customer->address]);
        }
        
        $fileName = 'customers_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];
        
        return response()->streamDownload(function () use ($csv) {
            echo $csv->getContent();
        }, $fileName, $headers);
    }

    public function exportPdf()
    {
        $customers = $this->getFilteredCustomers();
        
        $html = View::make('customers.export', ['customers' => $customers])->render();
        
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        
        $fileName = 'customers_' . date('Y-m-d') . '.pdf';
        
        return $pdf->stream($fileName);
    }
    private function getFilteredCustomers()
    {
        $user = auth()->user();
        $customers = Customer::forUser($user);
        
        if (request('search')) {
            $customers = $customers->where('first_name', 'LIKE', "%".request('search')."%");
        }
        return $customers->latest()->get();
    }
}
