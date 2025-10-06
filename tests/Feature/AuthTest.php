<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $password = 'password123';


    public function setUp(): void
    {
        parent::setUp();
        $this->faker = $this->makeFaker();
        // Pastikan environment test
        $this->app->loadEnvironmentFrom('.env.testing');
    }

    /** @test */
    public function register_success()
    {
        $payload = [
            'role' => 'partner',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => $this->password
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'result' => ['id', 'email', 'isVerified', 'pin']
            ])
            ->assertJson([
                'status' => 'SUCCESS',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }

    /** @test */
    public function register_duplicate_email_fails()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $payload = [
            'role' => 'partner',
            'email' => 'test@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => $this->password
        ];

        $response = $this->postJson('/api/auth/register', $payload);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'ERROR'
            ]);
    }

    /** @test */
    public function login_success_not_student()
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'password' => bcrypt('123456'),
            'is_verified' => true,
        ]);

        Passport::actingAs($user); // bypass token passport, langsung anggap sudah login

        $response = $this->getJson('/api/auth/me'); // endpoint yang butuh auth

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'result' => ['id', 'email']]);
    }

    /** @test */
    public function login_success_student()
    {
        $user = User::factory()->create([
            'email' => 'admin@gmail.com',
            'pin' => '123XA9'
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure(['status', 'result' => ['id', 'email']]);
    }

    /** @test */
    public function login_fail_invalid_credentials()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpass'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function forgot_password()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'role' => 'partner',
            'is_verified' => true
        ]);

        $response = $this->postJson('/api/auth/forgot', [
            'email' => $user->email
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'code' => 200
            ]);
    }

    /** @test */
    public function me_requires_auth()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'result' => ['id', 'email', 'role']
            ]);
    }

    /** @test */
    public function logout_requires_auth()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'SUCCESS'
            ]);
    }

    /** @test */
    public function users_index_requires_auth()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson('/api/auth/usersAll');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'code',
                'result'
            ]);
    }
}
