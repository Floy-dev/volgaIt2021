<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Service\Game\GameDto\GameDto;
use App\Service\Game\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Throwable;

class RoomController extends Controller
{
    /**
     * @var GameService
     */
    private $gameService;

    /**
     * @param GameService $gameService
     */
    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    /**
     * @throws BusinessException
     */
    public function room(string $uuid)
    {
        $dto = new GameDto();
        $dto->setId($uuid);

        $game = $this->gameService->getGame($dto);
        $field = $game->getAttribute('field');

        return view('room', [
            'cells' => $field->getAttribute('cells'),
            'width' => $field->getAttribute('width'),
            'height' => $field->getAttribute('height'),
            'currentPlayerId' => $game->getAttribute('currentPlayerId'),
            'colors' => $this->gameService->getColors(),
            'id' => $game->getAttribute('id'),
            'inc' => 0
        ]);
    }


    /**
     * @throws BusinessException|Throwable
     */
    public function roomCells(string $uuid): Response
    {
        $dto = new GameDto();
        $dto->setId($uuid);

        $game = $this->gameService->getGame($dto);
        $field = $game->getAttribute('field');

        $view = view('room_cells',[
            'cells' => $field->getAttribute('cells'),
            'width' => $field->getAttribute('width'),
            'height' => $field->getAttribute('height'),
            'currentPlayerId' => $game->getAttribute('currentPlayerId'),
            'inc' => 0,
        ])->render();

        return new Response(['view' => $view], 200);
    }
}
