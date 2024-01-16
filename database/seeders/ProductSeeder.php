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
        $productNames = [
            'Angle grinder',
            'Power drill',
            'Circular saw',
            'Pipe wrench',
            'Impact driver',
            'Jigsaw',
            'Belt sander',
            'Reciprocating saw',
            'Concrete mixer',
            'Rotary tool'
        ];

        foreach ($productNames as $productName) {
            Product::create([
                'name' => $productName,
                'description' => Str::random(50),
                'image' => '',
                'barcode' => Str::random(8),
                'price' => rand(100, 1000) / 10.0,
                'quantity' => rand(1, 15),
                'status' => 1,
                'user_id' => 1,
                'white_label_id' => 1,
            ]);

            Product::create([
                'name' => $productName,
                'description' => Str::random(50),
                'image' => '',
                'barcode' => Str::random(8),
                'price' => rand(100, 1000) / 10.0,
                'quantity' => rand(1, 15),
                'status' => 1,
                'user_id' => 1,
                'white_label_id' => 2,
            ]);
        }
    }
}
