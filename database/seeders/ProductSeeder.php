<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            Product::create([
                'name' => Str::random(10),
                'description' => Str::random(50),
                'image' => '',
                'barcode' => Str::random(8),
                'price' => rand(100, 1000) / 10.0,
                'quantity' => rand(1, 100),
                'status' => 1,
                'user_id' => 1,
                'white_label_id' => 1,
            ]);
        }
    }
}
