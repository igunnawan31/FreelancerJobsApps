<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
 
    public function test_login_user()
    {
        $user = User::factory()->create([
            'name' => 'test123',
            'email' => 'test123@example.com',
            'password' => bcrypt('password123'),
            'role' => 'freelancer'
        ]);

        $response = $this->post('login', [
            'email' => 'test123@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('logout');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }
}
