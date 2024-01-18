<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Csv\Writer;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $products = new Product();
        $products = $products->forUser($user);
        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }
        if ($request->search) {
            $products = $products->where('name', 'LIKE', "%{$request->search}%");
        }
        if ($request->active) {
            $products = $products->where('status', 1);
        }
        $products = $products->latest()->paginate(config('settings.pagination'))->appends(request()->except('page'));
        if (request()->wantsJson()) {
            return ProductResource::collection($products);
        }
        return view('products.index')->with('products', $products);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        $image_path = '';

        if ($request->hasFile('image')) {
            $image_path = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image_path,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'status' => $request->status,
            'user_id' => $request->user()->id,
            'white_label_id' => $request->user()->white_label_id
        ]);

        if (!$product) {
            return redirect()->back()->with('error', __('product.error_creating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_creating'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('products.edit')->with('product', $product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->barcode = $request->barcode;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::delete($product->image);
            }
            // Store image
            $image_path = $request->file('image')->store('products', 'public');
            // Save to Database
            $product->image = $image_path;
        }

        if (!$product->save()) {
            return redirect()->back()->with('error', __('product.error_updating'));
        }
        return redirect()->route('products.index')->with('success', __('product.success_updating'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::delete($product->image);
        }
        $product->delete();

        return response()->json([
            'success' => true
        ]);
    }
    public function exportCsv()
    {
        $products = $this->getFilteredProducts();
        
        $csv = Writer::createFromString('');
        $csv->insertOne([
            __('product.Name'),
            __('product.Image'),
            __('product.Barcode'),
            __('product.Price'),
            __('product.Quantity'),
            __('product.Status'),
            __('product.Created_At'),
            __('product.Updated_At'),
        ]);
        
        foreach ($products as $product) {
            $csv->insertOne([
                $product->name,
                asset(Storage::url($product->image)),
                $product->barcode,
                $product->price,
                $product->quantity,
                $product->status ? __('common.Active') : __('common.Inactive'),
                $product->created_at,
                $product->updated_at,
            ]);
        }
        
        $fileName = 'products_' . date('Y-m-d') . '.csv';
        $csv->output($fileName);
        die;
    }

    public function exportPdf()
    {
        $products = $this->getFilteredProducts();
        
        $html = View::make('products.export', ['products' => $products])->render();
        
        $pdf = new Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        
        $fileName = 'products_' . date('Y-m-d') . '.pdf';
        return $pdf->stream($fileName);
    }
    

    private function getFilteredProducts()
    {
        $user = auth()->user();
        $products = Product::forUser($user);
        
        if (request('search')) {
            $products = $products->where('name', 'LIKE', "%".request('search')."%");
        }
        
        if (request('active')) {
            $products = $products->where('status', 1);
        }
        
        return $products->latest()->get();
    }

}
