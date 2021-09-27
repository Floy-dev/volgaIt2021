<?php

namespace App\Service\Game;

use App\Cell;
use App\Field;
use App\Game;
use App\Player;
use App\Service\Game\Dto\GameDto;
use Illuminate\Support\Facades\DB;

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
                'color' => self::colors[rand(0, 6)]
            ]);
            $player->game()->associate($game)->save();
            $player->save();
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

        for ($i = 0; $i < 5; $i++){
            for ($j = 0; $j < 10; $j++){

                $cell = new Cell();

                $players = DB::table('players')->where('game_id', $game->getAttribute('id'))->get('id');

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
