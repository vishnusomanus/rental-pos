<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\CustomerImport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerImportController extends Controller
{
    public function index()
    {
        return view('imports.customer');
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new CustomerImport, $file);

        return redirect()->route('customers.index')->with('success', 'Customers imported successfully.');
    }
}
