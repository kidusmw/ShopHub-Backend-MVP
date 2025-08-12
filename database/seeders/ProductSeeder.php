<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductImage;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the product
        $product = Product::create([
            'title' => 'T-Shirt',
            'description' => 'High quality cotton t-shirt',
            'user_id' => 1,  // vendor user id
            'category_id' => 1,
            'price' => 20.00,
            'discount_price' => null,
            'status' => 'available',
        ]);

        // Add product images
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => '/images/tshirt-front.jpg',
        ]);
        ProductImage::create([
            'product_id' => $product->id,
            'image_path' => '/images/tshirt-back.jpg',
        ]);
    }
}
