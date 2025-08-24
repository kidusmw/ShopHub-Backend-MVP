<?php

use App\Models\User;
use App\Models\Category;
use function Pest\Laravel\{getJson, postJson, putJson, deleteJson, actingAs};

// ---------- INDEX ----------
it('returns all categories with children', function () {
    $user = User::factory()->create();

    actingAs($user, 'sanctum');

    $parent = Category::create(['name' => 'Parent Category']);
    $child = Category::create(['name' => 'Child Category', 'parent_id' => $parent->id]);

    getJson('/api/categories')
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Parent Category'])
        ->assertJsonFragment(['name' => 'Child Category']);
});

// ---------- STORE ----------
it('creates a new category', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $payload = ['name' => 'New Category', 'parent_id' => null];

    postJson('/api/categories', $payload)
        ->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'category' => ['id', 'name', 'parent_id', 'created_at', 'updated_at']
        ])
        ->assertJson(['category' => ['name' => 'New Category', 'parent_id' => null]]);
});

it('returns validation error if name is missing', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    postJson('/api/categories', ['name' => null])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

it('creates a category with a parent', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $parent = Category::create(['name' => 'Parent']);

    $payload = ['name' => 'Child', 'parent_id' => $parent->id];

    postJson('/api/categories', $payload)
        ->assertStatus(201)
        ->assertJson(['category' => ['name' => 'Child', 'parent_id' => $parent->id]]);
});

// ---------- SHOW ----------
it('returns a single category with parent and children', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $parent = Category::create(['name' => 'Parent']);
    $child = Category::create(['name' => 'Child', 'parent_id' => $parent->id]);

    getJson("/api/categories/{$parent->id}")
        ->assertStatus(200)
        ->assertJsonFragment(['name' => 'Parent'])
        ->assertJsonFragment(['name' => 'Child']);
});

// ---------- UPDATE ----------
it('updates a category', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $category = Category::create(['name' => 'Old Name']);

    $payload = ['name' => 'Updated Name'];

    putJson("/api/categories/{$category->id}", $payload)
        ->assertStatus(200)
        ->assertJson(['category' => ['name' => 'Updated Name']]);
});

it('returns validation error for invalid parent_id', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $category = Category::create(['name' => 'Old Name']);

    // Include name to pass required validation
    putJson("/api/categories/{$category->id}", ['name' => 'Old Name', 'parent_id' => 999])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['parent_id']);
});

// ---------- DESTROY ----------
it('deletes a category', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    $category = Category::create(['name' => 'To Delete']);

    deleteJson("/api/categories/{$category->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'Category deleted successfully']);
});

it('fails to delete non-existent category', function () {
    $user = User::factory()->create();
    actingAs($user, 'sanctum');

    deleteJson("/api/categories/999")
        ->assertStatus(404);
});
