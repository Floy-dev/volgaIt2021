<?php

namespace App\Service\Game;

use App\Cell;
use App\Exceptions\BusinessException;
use App\Field;
use App\Game;
use App\Player;
use App\Service\Game\GameDto\GameDto;
use App\Service\Game\MoveGameDto\MoveGameDto;
use App\Service\Game\NewGameDto\NewGameDto;
use App\User;
use Illuminate\Support\Facades\DB;

class GameService
{
    const colors = ['blue', 'green', 'cyan', 'red', 'magenta', 'yellow', 'white'];

    /**
     * @param NewGameDto $dto
     * @return Game
     */
    public function makeGame(NewGameDto $dto): Game
    {
        $game = new Game();
        $game->save();

        $this->makePlayers($game);
        $this->makeField($dto, $game);

        return $game;
    }

    /**
     * @param GameDto $dto
     * @return Game
     * @throws BusinessException
     */
    public function getGame(GameDto $dto): ?Game
    {
        $game = Game::where('id', $dto->getId())->first();
        if (!$game){
            throw new BusinessException('Игра с указанным ID не существует', 404);
        }
        return $game;
    }

    /**
     * @param MoveGameDto $dto
     * @return void
     * @throws BusinessException
     */
    public function moveGame(MoveGameDto $dto): void
    {
        $game = Game::where('id', $dto->getGameId())->first();
        if (!$game){
            throw new BusinessException('Игра с указанным ID не существует', 404);
        }

        $player = Player::where('id', $dto->getPlayerId())->first();
        if (!$player || !$player->getAttribute('game_id') == $dto->getGameId()){
            throw new BusinessException('Игрок с указанным номером не может выбрать указанный цвет', 404);
        }

        $field = Field::where('game_id', $dto->getGameId())->first();
        $cells = Cell::where('field_id', $field->getAttribute('id'))->get();

        dd($cells);
    }

    public function makePlayers(Game $game){
        $players = [];
        for ($i = 0; $i < 2; $i++){
            $player = new Player();
            $player->fill([
                'color' => self::colors[rand(0, 6)]
            ]);
            $player->game()->associate($game)->save();
            $player->save();
            $players[] = $player;
        }
        $game->players()->saveMany($players);
        $game->update([
            'currentPlayerId' => $game->getAttribute('players')[0]->getAttribute('id')
        ]);
        $game->save();
    }

    /**
     * @param NewGameDto $dto
     * @param Game $game
     * @return void
     */
    private function makeField(NewGameDto $dto, Game $game): void
    {
        $field = new Field();
        $field->fill([
            'width' => $dto->getWidth(),
            'height' => $dto->getHeight(),
        ]);
        $field->save();

        $this->makeCells($game, $field);

        $game->field()->save($field);
        $field->save();

        $field->game()->associate($game)->save();
    }


    private function makeCells(Game $game, Field $field){
        $cells = [];
        $players = DB::table('players')->where('game_id', $game->getAttribute('id'))->get('id');

        for ($i = 0; $i < $field->getAttribute('height'); $i++){
            for ($j = 0; $j < $field->getAttribute('width'); $j++){

                $cell = new Cell();

                if ($i == 0 && $j == 0){
                    $cell->fill([
                        'color' => self::colors[rand(0, 6)],
                        'playerId' => $players[0]->id
                    ]);
                }

                else if ($i == 4 && $j == 9){
                    $cell->fill([
                        'color' => self::colors[rand(0, 6)],
                        'playerId' => $players[1]->id
                    ]);
                }

                else {
                    $cell->fill([
                        'color' => self::colors[rand(0, 6)],
                        'playerId' => 0
                    ]);
                }

                $cells[] = $cell;
                $cell->field()->associate($field)->save();
                $cell->save();
            }
        }

        $field->cells()->saveMany($cells);
    }
}
