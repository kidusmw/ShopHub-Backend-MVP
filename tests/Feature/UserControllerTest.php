<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => 'admin'
    ]);
});

it('lists all users', function () {
    actingAs($this->admin, 'sanctum');

    $users = User::factory()->count(3)->create();

    getJson('/api/users')
        ->assertStatus(200)
        ->assertJsonCount(User::count());
});

it('creates a new user', function () {
    actingAs($this->admin, 'sanctum');

    $payload = [
        'name' => 'Jack Doe',
        'email' => 'jackdoe@example.com',
        'password' => 'password123',
        'role' => 'vendor',
    ];

    postJson('/api/users', $payload)
        ->assertStatus(201)
        ->assertJson(
            fn(AssertableJson $json) =>
            $json->where('message', 'User created successfully')
                ->where('user.name', 'Jack Doe')
                ->where('user.email', 'jackdoe@example.com')
                ->etc()
        );
});

it('fails to create user with invalid data', function () {
    actingAs($this->admin, 'sanctum');

    postJson('/api/users', [
        'name' => 'a',
        'email' => 'invalid-email',
        'password' => '123',
        'role' => 'invalid-role'
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            "email",
            "password"
        ]);
});

it('shows a single user', function () {
    actingAs($this->admin, 'sanctum');

    $user = User::factory()->create();

    getJson("/api/users/{$user->id}")
        ->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) =>
            $json->where('id', $user->id)
                ->where('email', $user->email)
                ->etc()
        );
});

it('updates a user', function () {
    actingAs($this->admin, 'sanctum');

    $user = User::factory()->create();

    $payload = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword',
        'role' => 'vendor',
    ];

    putJson("/api/users/{$user->id}", $payload)
        ->assertStatus(200)
        ->assertJson(
            fn(AssertableJson $json) =>
            $json->where('message', 'User updated successfully')
                ->where('user.name', 'Updated Name')
                ->where('user.email', 'updated@example.com')
                ->where('user.role', 'vendor')
                ->etc()
        );

    $user->refresh();
    expect(Hash::check('newpassword', $user->password))->toBeTrue();
});

it('fails update with invalid data', function () {
    $user = User::factory()->create();

    actingAs($this->admin, 'sanctum');

    $postData = [
        'name' => '',                  // empty to trigger 'required'
        'email' => 'not-an-email',     // invalid format
        'password' => '123',           // too short
        'role' => 'invalid-role'       // not in allowed list
    ];

    putJson("/api/users/{$user->id}", $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
});

it('deletes a user', function () {
    actingAs($this->admin, 'sanctum');

    $user = User::factory()->create();

    deleteJson("/api/users/{$user->id}")
        ->assertStatus(200)
        ->assertJson(['message' => 'User deleted successfully']);

    expect(User::find($user->id))->toBeNull();
});
