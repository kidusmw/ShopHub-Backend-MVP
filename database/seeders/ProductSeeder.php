<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'T-Shirt',
            'description' => 'High quality cotton t-shirt',
            'vendor_id' => 1,
            'category_id' => 1,
            'price' => 20.00
        ]);
    }
}
