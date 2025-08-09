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
            'title' => 'T-Shirt', // use title, not name
            'description' => 'High quality cotton t-shirt',
            'user_id' => 1,       // vendor_id is user_id here
            'category_id' => 1,
            'price' => 20.00,
            'discount_price' => null,
            'status' => 'available',
        ]);
    }
}
