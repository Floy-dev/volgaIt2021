<?php

namespace Tests\Feature;

use App\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetGameTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get("/game/" . Game::all()[1]->getAttribute('id'));
        $response->assertSuccessful();
        $response->assertStatus(200);
    }
}
