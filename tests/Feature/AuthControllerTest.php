<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;

// Use the RefreshDatabase trait for all tests in this file
uses(RefreshDatabase::class);

// beforeEach(function () {
//     // This runs before each test
// });

/**
* @test for register functionality
*/
it('can register a new user', function () {
    $data = [
        'name' => 'test',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = post('/api/register', $data);

    $response->assertStatus(201)
             ->assertJsonStructure([
                 'message',
                 'user' => ['id', 'name', 'email', 'created_at', 'updated_at'],
                 'access_token',
                 'token_type',
             ]);

    // Check if the user was created in the database
    $user = User::first();
    expect($user->name)->toBe('test')
        ->and($user->email)->toBe('test@example.com')
        ->and(Hash::check('password123', $user->password))->toBeTrue();
});

/**
* @test for registration with duplicate fields
*/
it('cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $data = [
        'name' => 'test2',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = post('/api/register', $data);
    $response->assertStatus(422);
});

/**
* @test for login functionality
*/
it('can login a user', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = post('/api/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure([
                 'message',
                 'user' => ['id', 'name', 'email'],
                 'access_token',
                 'token_type',
             ]);
});

/**
* @test for login with invalid credentials
*/
it('cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('123password'),
    ]);

    $response = post('/api/login', [
        'email' => 'test1@example.com',
        'password' => '1234password',
    ]);

    $response->assertStatus(401)
             ->assertJson(['message' => 'Invalid credentials']);
});

/**
* @test for logout functionality
*/
it('can logout a user', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/logout');
    $response->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully']);
});

/**
* @test for password reset link request
*/
it('can request a password reset link', function () {
    $user = User::factory()->create();

    // Mock the Password facade to avoid sending real emails
    Password::shouldReceive('sendResetLink')
        ->once()
        ->with(['email' => $user->email])
        ->andReturn(Password::RESET_LINK_SENT);

    $response = post('/api/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => __(Password::RESET_LINK_SENT)]);
});

