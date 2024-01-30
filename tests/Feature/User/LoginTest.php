<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_with_mo_data(): void
    {
        $credentials = [
            // no data
        ];

        $response = $this->postJson('/login', $credentials);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'email' => 'The email field is required.',
            'password' => 'The password field is required.'
        ]);
    }

    public function test_login_with_invalid_field_types(): void
    {
        $credentials = [
            'email' => 123,
            'password' => 123
        ];

        $response = $this->postJson('/login', $credentials);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'email' => 'The email must be a string.',
            'password' => 'The password must be a string.'
        ]);
    }

    public function test_login_with_invalid_field_formats(): void
    {
        $credentials = [
            'email' => ['name@email.com'],
            'password' => ['password']
        ];

        $response = $this->postJson('/login', $credentials);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'email' => 'The email must be a string.',
            'password' => 'The password must be a string.'
        ]);
    }

    public function test_login_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'wrong password'
        ];

        $response = $this->postJson('/login', $credentials);
        $response->assertUnauthorized();

        static::assertFalse(Auth::check());
    }

    public function test_login_with_wrong_email(): void
    {
        User::factory()->create();

        $credentials = [
            'email' => 'wrong@email.com',
            'password' => 'wrong password'
        ];

        $response = $this->postJson('/login', $credentials);
        $response->assertUnauthorized();

        static::assertFalse(Auth::check());
    }

    public function test_login(): void
    {
        $password = 'password12345';
        $user = User::factory()->create(['password' => Hash::make($password)]);
        User::factory()->create(); // another user

        $credentials = [
            'email' => $user->email,
            'password' => $password
        ];

        $response = $this->postJson('/login', $credentials);
        $response->assertOk();

        $this->assertAuthenticatedAs($user);
    }
}
