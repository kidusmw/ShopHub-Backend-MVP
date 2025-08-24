<?php

namespace Database\Factories;

use App\Models\ProductImage;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition()
    {
        Storage::fake('public');
        return [
            'product_id' => Product::factory(),
            'image_path' => UploadedFile::fake()->image('product.jpg')->store('products', 'public'),
        ];
    }
}
