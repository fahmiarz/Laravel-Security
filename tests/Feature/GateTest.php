<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class GateTest extends TestCase
{
    public function testGate()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where("email", "arzalega@localhost")->first();
        Auth::login($user);

        $contact = Contact::where("email", "test@localhost")->first();
        self::assertTrue(Gate::allows('get-contacts', $contact));;
        self::assertTrue(Gate::allows('update-contacts', $contact));;
        self::assertTrue(Gate::allows('delete-contacts', $contact));;
    }

    public function testGateMethod()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where("email", "arzalega@localhost")->first();
        Auth::login($user);

        $contact = Contact::where("email", "test@localhost")->first();
        self::assertTrue(Gate::allows('get-contacts', $contact));;
        self::assertTrue(Gate::allows('update-contacts', $contact));;
        self::assertTrue(Gate::allows('delete-contacts', $contact));;

        self::assertTrue(Gate::any(['get-contacts', 'update-contacts', 'delete-contacts'], $contact));
        self::assertFalse(Gate::none(['get-contacts', 'update-contacts', 'delete-contacts'], $contact));
    }

    public function testGateNonLogin()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where("email", "arzalega@localhost")->first();
        $gate = Gate::forUser($user);

        $contact = Contact::where("email", "test@localhost")->first();
        self::assertTrue($gate->allows('get-contacts', $contact));;
        self::assertTrue($gate->allows('update-contacts', $contact));;
        self::assertTrue($gate->allows('delete-contacts', $contact));;
    }

    public function testGateResponse()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $user = User::where("email", "arzalega@localhost")->first();
        Auth::login($user);

        $response = Gate::inspect("create-contacts");
        self::assertFalse($response->allowed());
        self::assertEquals("You are not admin", $response->message());
    }
}
