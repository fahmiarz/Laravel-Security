<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testAuth()
    {
        $this->seed(UserSeeder::class);

        $success = Auth::attempt([
            "email" => "arzalega@localhost",
            "password" => "rahasia12345"
        ],true);

        self::assertTrue($success);

        $user = Auth::user();
        self::assertNotNull($user);
        self::assertEquals("arzalega@localhost", $user->email);
    }

    public function testGuest()
    {
        $user = Auth::user();
        self::assertNull($user);
    }

    public function testLogin()
    {
        $this->seed(UserSeeder::class);

        $this->get('/users/login?email=arzalega@localhost&password=rahasia12345')
            ->assertRedirect('/users/current');

        $this->get('/users/login?email=salah@localhost&password=rahasia12345')
            ->assertSeeText("wrong credentials");
    }

    public function testCurrent()
    {
        $this->seed(UserSeeder::class);

        $this->get('/users/current')
            ->assertStatus(302)
            ->assertRedirect('/login');  // belum login

        $user = User::where("email", "arzalega@localhost")->firstOrFail();
        $this->actingAs($user) //actingAs digunakan untuk meregisterkan ke session
             ->get('users/current')
             ->assertSeeText("Hello Arzalega");

    }

    public function testTokenGuard()
    {
        $this->seed(UserSeeder::class);

        $this->get('/api/users/current',
        [
            'Accept' => 'application/json',
        ])
            ->assertStatus(401);

        $this->get('/api/users/current',
        [
            'Accept' => 'application/json',
            'API-KEY' => 'secret'
        ])
            ->assertSeeText("Hello Arzalega");
    }

    public function testUserProvider()
    {
        $this->seed(UserSeeder::class);

        $this->get('/simple-api/users/current',
            [
                'Accept' => 'application/json',
            ])
            ->assertStatus(401);

        $this->get('/simple-api/users/current',
            [
                'Accept' => 'application/json',
                'API-KEY' => 'secret'
            ])
            ->assertSeeText("Hello Arzalega");
    }

}
