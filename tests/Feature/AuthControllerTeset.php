use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use function Pest\Laravel\post;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    // This runs before each test
});

/**
* @test for register functionality
*/
it('can register a new user', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
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

    // Check the password is hashed
    expect(User::first()->password)->not->toBe('password123');
});

/**
* @test for registration with duplicate fields
*/
it('cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'john@example.com']);

    $data = [
        'name' => 'Jane Doe',
        'email' => 'john@example.com',
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
             ->assertJson(['message' => 'Unauthorized']);
});

/**
* @test for logout functionality
*/
it('can logout a user', function () {
    $user = User::factory()->create();

    $response = actingAs($user)->post('/api/logout');

    $response->assertStatus(200)
             ->assertJson(['message' => 'Logged out successfully']);
});

/**
* @test for password reset link request
*/
it('can request a password reset link', function () {
    $user = User::factory()->create();

    $response = post('/api/forgot-password', [
        'email' => $user->email,
    ]);

    $response->assertStatus(200)
             ->assertJsonStructure(['message']);
});
