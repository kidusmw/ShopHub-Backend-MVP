<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Variant;
use App\Models\Category;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    actingAs($this->admin, 'sanctum');

    $this->category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    $this->product = Product::create([
        'user_id'     => $this->admin->id,
        'category_id' => $this->category->id,
        'title'       => 'Test Product',      // ✅ required
        'slug'        => 'test-product',
        'description' => 'Test description',
        'price'       => 100,
        'status'      => 'available',
    ]);

    $this->variant = Variant::create([
        'product_id' => $this->product->id,
        'name' => 'Test Variant',
        'sku' => 'TV123',
        'price' => 50,
        'stock' => 10,
    ]);
});

it('updates a variant', function () {
    $data = [
        'sku' => 'SKU456',
        'stock' => 15,
        'price' => 149.99,
    ];

    putJson("/api/variants/{$this->variant->id}", $data)
        ->assertStatus(200)
        ->assertJsonFragment(['sku' => 'SKU456']);
});

it('fails to update with invalid data', function () {
    $data = [
        'sku' => '', // invalid
        'stock' => -5, // invalid
        'price' => -10, // invalid
    ];

    putJson("/api/variants/{$this->variant->id}", $data)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['sku', 'stock', 'price']);
});

it('deletes a variant', function () {
    deleteJson("/api/variants/{$this->variant->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Variant deleted']);

    $this->assertDatabaseMissing('variants', ['id' => $this->variant->id]);
});
