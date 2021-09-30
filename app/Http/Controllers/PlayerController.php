<?php

namespace App\Http\Controllers;

use App\Player;
use Illuminate\Http\Response;

class PlayerController extends Controller
{
    /**
     * @param int $id
     * @return Response
     */
    public function player(int $id): Response
    {
        $player = Player::where('id', $id)->first();
        return new Response(['color' => $player->getAttribute('color')], 200);
    }
}
