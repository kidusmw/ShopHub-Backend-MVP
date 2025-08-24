<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Variant;
use App\Models\Category;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

use function Pest\Laravel\{getJson, actingAs};

uses(TestCase::class, DatabaseTransactions::class)->in('Feature');

it('returns 401 if no active cart exists', function () {
    // Create a user
    $user = User::factory()->create();

    // Act as the created user and make a GET request to the cart endpoint
    actingAs($user, 'sanctum');

    // Ensure no active cart exists for the user
    getJson('/api/cart')
        ->assertStatus(404)
        ->assertJson(['message' => 'No active cart found']);
});

it('returns active cart with items and total', function () {
    // Create a user
    $user = User::factory()->create();

    // Create a category (if needed for the product)
    $category = Category::create([
        'name' => 'Test Category',
    ]);

    // Create product & variant manually
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100, // optional, depends on variant
    ]);

    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'price' => 100,
        'sku' => 'TESTSKU001',
    ]);

    // Create cart manually
    $cart = Cart::create([
        'user_id' => $user->id,
        'order_status' => false
    ]);

    // Add cart item manually
    CartItem::create([
        'cart_id' => $cart->id,
        'variant_id' => $variant->id,
        'quantity' => 2
    ]);

    // Act as the created user and make a GET request to the cart endpoint
    actingAs($user, 'sanctum');

    getJson('/api/cart')
        ->assertStatus(200)
        ->assertJsonStructure([
            'id',
            'user_id',
            'order_status',
            'items' => [
                '*' => [
                    'id',
                    'cart_id',
                    'variant_id',
                    'quantity',
                    'variant' => [
                        'id',
                        'product_id',
                        'price',
                    ],
                ],
            ],
            'cart_total',
        ])->assertJson([
            'id' => $cart->id,
            'user_id' => $user->id,
            'order_status' => false,
            'cart_total' => 200, // 2 items * 100 price each
        ]);

});
