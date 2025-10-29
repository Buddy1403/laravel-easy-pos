<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'test product',
            'description' => 'This is a test product description.',
            'image' => '',
            'quantity' => 50,
            'barcode' => '1234567890123',
            'regular_price' => 100.00,
            'selling_price' => 120.00,
            'status' => true,
        ]);
    }
}
