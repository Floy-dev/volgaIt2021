<?php

namespace App\Service\Game;

use App\Cell;
use App\Field;
use App\Game;
use App\Player;
use App\Service\Game\Dto\GameDto;

class GameService
{
    const colors = ['blue', 'green', 'cyan', 'red', 'magenta', 'yellow', 'white'];

    /**
     * @param GameDto $dto
     * @return Game
     */
    public function makeGame(GameDto $dto): Game
    {
        $game = new Game();
        $game->save();

        $this->makePlayers($game);
        $this->makeField($dto, $game);

        return $game;
    }


    public function makePlayers(Game $game){
        $players = [];
        for ($i = 0; $i < 2; $i++){
            $player = new Player();
            $player->fill([
                'color' => self::colors[rand(0, 9)]
            ]);
            $player->save();
            $player->game()->associate($game)->save();
            $players[] = $player;
        }
        $game->players()->saveMany($players);
    }

    /**
     * @param GameDto $dto
     * @param Game $game
     * @return void
     */
    private function makeField(GameDto $dto, Game $game): void
    {
        $field = new Field();

        $cells = $this->makeCells();

        $field->fill([
            'width' => $dto->getWidth(),
            'height' => $dto->getHeight(),
        ]);

        $game->field()->save($field);
        $field->game()->associate($game)->save();
    }


    private function makeCells(){
        $cells = [];

        for ($i = 0; $i < 5; $i++){
            for ($j = 0; $j < 10; $j++){

                $cell = new Cell();
                $cell->fill([
                    'color' => self::colors[rand(0, 9)],
                    'playerId' => 0
                ]);

                $cells[$i][$j] = $cell;
            }
        }
    }
}
