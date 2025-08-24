<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\{getJson, postJson, putJson, deleteJson, actingAs};

// ---------- INDEX ----------
it('returns paginated products with status available', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');
    $category = Category::create(['name' => 'Test Category']);

    // Create 5 products manually
    for ($i = 0; $i < 5; $i++) {
        Product::create([
            'title' => "Product $i",
            'description' => "Description $i",
            'price' => 100 + $i,
            'discount_price' => 90 + $i,
            'status' => 'available',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    getJson('/api/products')
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

// ---------- STORE ----------
it('creates a new product', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');
    $category = Category::create(['name' => 'Test Category']);

    $payload = [
        'title' => 'Test Product',
        'description' => 'Product description',
        'user_id' => $user->id,
        'category_id' => $category->id,
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
    ];

    postJson('/api/products', $payload)
        ->assertStatus(201)
        ->assertJsonStructure(['message', 'product_id']);
});

it('fails validation when required fields are missing', function () {
    actingAs(User::factory()->create(), 'sanctum');

    postJson('/api/products', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'user_id', 'category_id', 'price', 'status']);
});

// ---------- SHOW ----------
it('returns a single product with details', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');
    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'title' => 'Test Product',
        'description' => 'Some description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    getJson("/api/products/{$product->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['title' => $product->title]);
});

// ---------- UPDATE ----------
it('updates a product', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');
    $category = Category::create(['name' => 'Test Category']);
    $product = Product::create([
        'title' => 'Old Title',
        'description' => 'Old description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => $user->id,
        'category_id' => $category->id,
    ]);

    $payload = ['title' => 'Updated Product'];

    postJson("/api/products/{$product->id}", $payload)
        ->assertStatus(200)
        ->assertJson(['message' => 'Product updated']);
});

it('fails update validation for invalid fields', function () {
    actingAs(User::factory()->create(), 'sanctum');

    $product = Product::create([
        'title' => 'Test Product',
        'description' => 'Some description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => User::factory()->create()->id,
        'category_id' => Category::create(['name' => 'Test Category'])->id,
    ]);

    postJson("/api/products/{$product->id}", ['price' => -10])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['price']);
});

// ---------- DELETE ----------
it('deletes a product', function () {
    actingAs(User::factory()->create(), 'sanctum');

    $product = Product::create([
        'title' => 'Test Product',
        'description' => 'Some description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => User::factory()->create()->id,
        'category_id' => Category::create(['name' => 'Test Category'])->id,
    ]);

    deleteJson("/api/products/{$product->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Product deleted successfully']);
});

it('fails to delete non-existent product', function () {
    actingAs(User::factory()->create(), 'sanctum');
    deleteJson("/api/products/999")
        ->assertStatus(404);
});

// ---------- IMAGE HANDLING ----------
it('uploads a single image', function () {
    actingAs(User::factory()->create(), 'sanctum');

    Storage::fake('public');

    $product = Product::create([
        'title' => 'Test Product',
        'description' => 'Some description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => User::factory()->create()->id,
        'category_id' => Category::create(['name' => 'Test Category'])->id,
    ]);

    $file = UploadedFile::fake()->image('product.jpg');

    postJson("/api/products/{$product->id}/images", ['image' => $file])
        ->assertStatus(200)
        ->assertJson(['message' => 'Product images updated successfully']);

    Storage::disk('public')->assertExists("products/{$file->hashName()}");
});

it('deletes a single product image', function () {
    actingAs(User::factory()->create(), 'sanctum');
    Storage::fake('public');
    $product = Product::create([
        'title' => 'Test Product',
        'description' => 'Some description',
        'price' => 100,
        'discount_price' => 90,
        'status' => 'available',
        'user_id' => User::factory()->create()->id,
        'category_id' => Category::create(['name' => 'Test Category'])->id,
    ]);

    $image = $product->images()->create([
        'image_path' => UploadedFile::fake()->image('delete.jpg')->store('products', 'public')
    ]);

    deleteJson("/api/products/{$product->id}/images/{$image->id}/delete")
        ->assertStatus(200)
        ->assertJson(['message' => 'Image deleted successfully']);

    Storage::disk('public')->assertMissing($image->image_path);
});
