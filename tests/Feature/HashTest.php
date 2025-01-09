<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use function PHPUnit\Framework\assertTrue;

class HashTest extends TestCase
{
    public function testHash()
    {
        $password = "rahasia";
        $hash = Hash::make($password);

        $result = Hash::check("rahasia", $hash);
        assertTrue($result);
    }
}
