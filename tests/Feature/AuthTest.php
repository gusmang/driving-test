<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{

    public function test_register_student_returns_pin()
    {
        $resp = $this->postJson('/api/auth/register', ['role' => 'student', 'email' => 's1@example.com']);
        $resp->assertStatus(200)->assertJsonPath('status', 'SUCCESS')->assertJsonStructure(['result' => ['id', 'email', 'isVerified', 'pin']]);
    }
}
