<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Customer;

class CustomerImport implements ToModel
{
    public function model(array $row)
    {
        return new Customer([
            'first_name' => $row[0],
            'last_name' => $row[1],
            'email' => $row[2],
            'phone' => $row[3],
            'address' => $row[4],
            'user_id' => auth()->id(),
            'white_label_id' => auth()->user()->white_label_id,
        ]);
    }
}
