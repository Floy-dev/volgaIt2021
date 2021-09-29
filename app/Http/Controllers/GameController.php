<?php

namespace App\Http\Controllers;

use App\Exceptions\BusinessException;
use App\Service\Game\GameDto\GameDto;
use App\Service\Game\GameService;
use App\Service\Game\MoveGameDto\MoveGameRequest;
use App\Service\Game\NewGameDto\NewGameRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

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
     * @param NewGameRequest $request
     * @return Response
     * @throws ValidationException
     */
    public function newGame(NewGameRequest $request): Response
    {
        $dto = $request->getDto();

        $game = $this->gameService->makeGame($dto);

        return new Response(['id' => $game->getAttribute('id')], 201);
    }

    /**
     * @param string $id
     * @return Response
     * @throws BusinessException
     */
    public function game(string $id): Response
    {
        if (!$id || !is_string($id)){
            throw new BusinessException('Неправильные параметры запроса', 400);
        }

        $dto = GameDto::getDto(['id' => $id]);

        $game = $this->gameService->getGame($dto);

        return new Response($game->toArray(), 200);
    }

    /**
     * @param MoveGameRequest $request
     * @return Response
     * @throws BusinessException
     * @throws ValidationException
     */
    public function moveGame(MoveGameRequest $request): Response
    {
        $dto = $request->getDto();

        $this->gameService->moveGame($dto);

        return new Response('Ход успешно сделан', 200);
    }
}
