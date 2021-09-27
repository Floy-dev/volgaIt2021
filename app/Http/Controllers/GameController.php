<?php

namespace App\Http\Controllers;

use App\Field;
use App\Game;
use App\Service\Game\Dto\GameRequest;
use App\Service\Game\GameService;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
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
     * @param GameRequest $request
     * @return Response
     * @throws ValidationException
     */
    public function game(GameRequest $request): Response
    {
        $dto = $request->getDto();

        $game = $this->gameService->makeGame($dto);

        return new Response(['id' => $game->getAttribute('id')], 201);
    }
}
