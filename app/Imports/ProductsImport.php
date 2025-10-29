<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    /**
     * @return Model|null
     */
    public function model(array $row)
    {
        $product = Product::where('name', $row['name'])->first();

        if ($product) {
            $product->quantity = $row['quantity'] ?? 0;
            $product->save();

            return null;
        }

        return new Product([
            'name' => $row['name'] ?? null,
            'barcode' => $row['barcode'] ?? null,
            'regular_price' => $row['regular_price'] ?? 0,
            'selling_price' => $row['selling_price'] ?? 0,
            'quantity' => $row['quantity'] ?? 0,
        ]);
    }
}
