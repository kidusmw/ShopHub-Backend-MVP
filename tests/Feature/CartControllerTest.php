<?php

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;

use function Pest\Laravel\{getJson, postJson, putJson, deleteJson, actingAs};

// ---------- INDEX ----------
it('returns 404 if no active cart exists', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');
    getJson('/api/cart')
        ->assertStatus(404)
        ->assertJson(['message' => 'No active cart found']);
});

it('returns active cart with items and total', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU001',
        'price' => 100,
    ]);

    $cart = Cart::create(['user_id' => $user->id, 'order_status' => false]);
    CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'quantity' => 2]);

    actingAs($user, 'sanctum');

    getJson('/api/cart')
        ->assertStatus(200)
        ->assertJsonStructure([
            'id', 'user_id', 'order_status', 'created_at', 'updated_at', 'items', 'cart_total'
        ])
        ->assertJson([
            'user_id' => $user->id,
            'cart_total' => 200,
        ]);
});

// ---------- STORE ----------
it('adds an item to the cart', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU002',
        'price' => 100,
    ]);

    actingAs($user, 'sanctum');

    $payload = ['variant_id' => $variant->id, 'quantity' => 3];

    $response = postJson('/api/cart', $payload);

    $response->assertStatus(201)
        ->assertJson([
            'variant_id' => $variant->id,
            'quantity' => 3,
        ]);
});

it('returns validation error if quantity is missing', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    postJson('/api/cart', ['variant_id' => 1])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['quantity']);
});

it('returns validation error if variant_id does not exist', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    postJson('/api/cart', ['variant_id' => 999, 'quantity' => 1])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['variant_id']);
});

// ---------- UPDATE ----------
it('updates the quantity of a cart item', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU003',
        'price' => 100,
    ]);

    $cart = Cart::create(['user_id' => $user->id, 'order_status' => false]);
    $item = CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'quantity' => 2]);

    actingAs($user, 'sanctum');

    putJson("/api/cart/items/{$item->id}", ['quantity' => 5])
        ->assertStatus(200)
        ->assertJson(['quantity' => 5]);
});

// ---------- DESTROY ----------
it('removes a cart item', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU004',
        'price' => 100,
    ]);

    $cart = Cart::create(['user_id' => $user->id, 'order_status' => false]);
    $item = CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'quantity' => 2]);

    actingAs($user, 'sanctum');

    deleteJson("/api/cart/items/{$item->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Cart item removed successfully']);
});

// ---------- CLEAR ----------
it('clears the cart', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU005',
        'price' => 100,
    ]);

    $cart = Cart::create(['user_id' => $user->id, 'order_status' => false]);
    CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'quantity' => 2]);

    actingAs($user, 'sanctum');

    deleteJson('/api/cart/clear')
        ->assertStatus(200)
        ->assertJson(['message' => 'Cart cleared successfully']);
});

// ---------- CHECKOUT ----------
it('checks out the cart', function () {
    $user = User::factory()->create();

    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'user_id' => $user->id,
        'category_id' => $category->id,
        'title' => 'Test Product',
        'description' => 'Test description',
        'price' => 100,
        'status' => 'available',
    ]);
    $variant = Variant::create([
        'product_id' => $product->id,
        'name' => 'Default Variant',
        'sku' => 'TESTSKU006',
        'price' => 100,
    ]);

    $cart = Cart::create(['user_id' => $user->id, 'order_status' => false]);
    CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'quantity' => 2]);

    actingAs($user, 'sanctum');

    postJson("/api/cart/checkout/{$cart->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Checkout successful']);
});
