<?php

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Variant;

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
