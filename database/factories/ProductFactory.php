<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(10, 500),
            'discount_price' => $this->faker->numberBetween(5, 450),
            'status' => 'available',
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }
}
