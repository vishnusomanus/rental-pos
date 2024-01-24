<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportController extends Controller
{
    public function index()
    {
        return view('imports.product');
    }

    public function store(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new ProductImport, $file);

        return redirect()->route('products.index')->with('success', 'Products imported successfully.');
    }
}
