<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GameCreateTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test()
    {
        $response = $this->post('/game', ['width' => 6, 'height' => 6]);
        $response->assertSuccessful();
        $response->assertStatus(201);
    }
}
