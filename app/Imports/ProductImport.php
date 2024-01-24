<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Product;

class ProductImport implements ToModel
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row[0],
            'description' => $row[1],
            'barcode' => $row[2],
            'price' => floatval($row[3]),
            'quantity' => intval($row[4]),
            'status' => intval($row[5]),
            'user_id' => auth()->id(),
            'white_label_id' => auth()->user()->white_label_id,
        ]);
    }
}
