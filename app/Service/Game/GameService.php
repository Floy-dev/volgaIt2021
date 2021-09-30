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
    const type = 'rhombus'; // rhombus | square

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
     * @return string
     */
    public function getType(): string
    {
        return self::type;
    }

    /**
     * @param MoveGameDto $dto
     * @return void
     * @throws BusinessException
     */
    public function moveGame(MoveGameDto $dto): void
    {
        /** @var Game $game */
        $game = Game::where('id', $dto->getGameId())->first();
        if (!$game){
            throw new BusinessException('Игра с указанным ID не существует', 404);
        }

        /** @var Player $player */
        $player = Player::where('id', $dto->getPlayerId())->first();
        if (!$player || !$player->getAttribute('game_id') == $dto->getGameId()){
            throw new BusinessException('Игрок с указанным ID не существует', 404);
        }

        if ($dto->getPlayerId() == 0){
            throw new BusinessException('Игра закончена', 400);
        }

        if ($game->getAttribute('winnerPlayerId') != 0){
            throw new BusinessException('Игра закончена', 400);
        }

        $enemy = $this->getEnemy($game, $dto);
        if ($enemy->getAttribute('color') == $dto->getColor() ||
            $player->getAttribute('color') == $dto->getColor() ||
            !in_array($dto->getColor(), self::colors)
        ){
            throw new BusinessException('Игрок с указанным номером не может выбрать указанный цвет', 409);
        }

        if ($game->getAttribute('currentPlayerId') == $enemy->getAttribute('id')){
            throw new BusinessException('Игрок с указанным номером не может сейчас ходить', 403);
        }

        $player->update([
           'color' => $dto->getColor()
        ]);
        $player->save();

        $field = Field::where('game_id', $dto->getGameId())->first();
        $cells = Cell::where('field_id', $field->getAttribute('id'))->get();

        $serializedCells = $this->serializeCells($cells, $field);
        $playerCells = $this->getPlayerCells($cells, $field, $player);
        $count = $this->getPlayerCellsCount($playerCells);

        for ($y = 0; $y < count($playerCells); $y++){
            for ($x = 0; $x < count($playerCells[$y]); $x++){
                if ($playerCells[$y][$x] == null) continue;
                self::type == 'rhombus'
                    ? $this->checkNeighborsRhombus($serializedCells, $x, $y, $player, $count)
                    : $this->checkNeighborsSquare($serializedCells, $x, $y, $player, $count);
            }
        }

        if ($count * 100 / ($field->getAttribute('width') * $field->getAttribute('height')) >= 45){
            $game->update([
               'winnerPlayerId' => $player->getAttribute('id')
            ]);
            $game->save();
        }

        $game->update([
            'currentPlayerId' => $enemy->getAttribute('id')
        ]);
        $game->save();
    }

    /**
     * @return string[]
     */
    public function getColors(): array
    {
        return self::colors;
    }

    /**
     * @param array $serializedCells
     * @param int $x
     * @param int $y
     * @param Player $player
     * @param int $count
     */
    public function checkNeighborsRhombus(array $serializedCells, int $x, int $y, Player $player, int &$count){
        if ($y % 2 == 0){
            if ($x - 1 >= 0 &&
                $y - 1 >= 0 &&
                $serializedCells[$y - 1][$x - 1]->getAttribute('color') == $player->getAttribute('color') &&
                $serializedCells[$y - 1][$x - 1]->getAttribute('playerId') == 0
            ){ $this->changeNeighborRhombus($serializedCells, $x - 1, $y - 1, $player, $count); }

            if ($x - 1 >= 0 &&
                $y + 1 < count($serializedCells) &&
                $serializedCells[$y + 1][$x - 1]->getAttribute('color') == $player->getAttribute('color') &&
                $serializedCells[$y + 1][$x - 1]->getAttribute('playerId') == 0
            ){ $this->changeNeighborRhombus($serializedCells, $x - 1, $y + 1, $player, $count); }
        }
        else{
            if ($x + 1 < count($serializedCells[$y]) &&
                $y + 1 < count($serializedCells) &&
                $serializedCells[$y + 1][$x + 1]->getAttribute('color') == $player->getAttribute('color') &&
                $serializedCells[$y + 1][$x + 1]->getAttribute('playerId') == 0
            ){ $this->changeNeighborRhombus($serializedCells, $x + 1, $y + 1, $player, $count); }

            if ($x + 1 < count($serializedCells[$y]) &&
                $y - 1 >= 0 &&
                $serializedCells[$y - 1][$x + 1]->getAttribute('color') == $player->getAttribute('color') &&
                $serializedCells[$y - 1][$x + 1]->getAttribute('playerId') == 0
            ){ $this->changeNeighborRhombus($serializedCells, $x + 1, $y - 1, $player, $count); }
        }

        if ($y - 1 >= 0 &&
            $serializedCells[$y - 1][$x]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y - 1][$x]->getAttribute('playerId') == 0
        ){ $this->changeNeighborRhombus($serializedCells, $x, $y - 1, $player, $count); }

        if ($y + 1 < count($serializedCells) &&
            $serializedCells[$y + 1][$x]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y + 1][$x]->getAttribute('playerId') == 0
        ){ $this->changeNeighborRhombus($serializedCells, $x, $y + 1, $player, $count); }
    }

    /**
     * @param array $serializedCells
     * @param int $x
     * @param int $y
     * @param Player $player
     * @param int $count
     */
    public function checkNeighborsSquare(array $serializedCells, int $x, int $y, Player $player, int &$count){
        if ($y - 1 >= 0 &&
            $serializedCells[$y - 1][$x]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y - 1][$x]->getAttribute('playerId') == 0
        ){ $this->changeNeighborSquare($serializedCells, $x, $y - 1, $player, $count); }

        if ($y + 1 < count($serializedCells) &&
            $serializedCells[$y + 1][$x]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y + 1][$x]->getAttribute('playerId') == 0
        ){ $this->changeNeighborSquare($serializedCells, $x, $y + 1, $player, $count); }

        if ($x - 1 >= 0 &&
            $serializedCells[$y][$x - 1]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y][$x - 1]->getAttribute('playerId') == 0
        ){ $this->changeNeighborSquare($serializedCells, $x - 1, $y, $player, $count); }

        if ($x + 1 < count($serializedCells[$x]) &&
            $serializedCells[$y][$x + 1]->getAttribute('color') == $player->getAttribute('color') &&
            $serializedCells[$y][$x + 1]->getAttribute('playerId') == 0
        ){ $this->changeNeighborSquare($serializedCells, $x + 1, $y, $player, $count); }
    }

    /**
     * @param array $serializedCells
     * @param int $x
     * @param int $y
     * @param Player $player
     * @param int $count
     */
    public function changeNeighborSquare(array $serializedCells, int $x, int $y, Player $player, int &$count){
        $serializedCells[$y][$x]->update([
            'playerId' => $player->getAttribute('id'),
            'color' => $player->getAttribute('color'),
        ]);
        $serializedCells[$y][$x]->setAttribute('playerId', $player->getAttribute('id'));
        $serializedCells[$y][$x]->setAttribute('color', $player->getAttribute('color') );
        $serializedCells[$y][$x]->save();
        $this->checkNeighborsSquare($serializedCells, $x, $y, $player, $count);
    }

    /**
     * @param array $serializedCells
     * @param int $x
     * @param int $y
     * @param Player $player
     * @param int $count
     */
    public function changeNeighborRhombus(array $serializedCells, int $x, int $y, Player $player, int &$count){
        $serializedCells[$y][$x]->update([
            'playerId' => $player->getAttribute('id'),
            'color' => $player->getAttribute('color'),
        ]);
        $serializedCells[$y][$x]->setAttribute('playerId', $player->getAttribute('id'));
        $serializedCells[$y][$x]->setAttribute('color', $player->getAttribute('color') );
        $serializedCells[$y][$x]->save();
        $this->checkNeighborsRhombus($serializedCells, $x, $y, $player, $count);
    }

    /**
     * @throws BusinessException
     */
    public function getEnemy(Game $game, MoveGameDto $dto): Player
    {
        $players = $game->getAttribute('players');
        /** @var Player $player */
        foreach ($players as $player) {
            if ($player->getAttribute('id') == $dto->getPlayerId()){continue;}
            return $player;
        }
        throw new BusinessException('В игре присутствует только один игрок', 404);
    }

    /**
     * @param object $cells
     * @param Field $field
     * @param Player $player
     * @return array
     */
    public function getPlayerCells(object $cells, Field $field, Player $player): array
    {
        $x = 0;
        $y = 0;
        /** @var Cell $cell */
        foreach ($cells as $cell){
            if ($x == $field->getAttribute('width')){ $x = 0; $y++; }
            if ($cell->getAttribute('playerId') == $player->getAttribute('id')){
                $temp[$y][] = $cell;
                $cell->update([
                    'color' => $player->getAttribute('color'),
                ]);
                $cell->save();
            }
            else{
                $temp[$y][] = null;
            }
            $x++;
        }
        return $temp ?? [];
    }

    /**
     * @param array $playerCells
     * @return int
     */
    public function getPlayerCellsCount(array $playerCells): int
    {
        $count = 0;
        foreach ($playerCells as $cells) {
            foreach ($cells as $cell){
                if ($cell != null){
                    $count++;
                }
            }
        }
        return $count;
    }

    /**
     * @param object $cells
     * @param Field $field
     * @return array
     */
    public function serializeCells(object $cells, Field $field): array
    {
        $x = 0;
        $y = 0;
        foreach ($cells as $cell){
            if ($x == $field->getAttribute('width')){ $x = 0; $y++; }
            $temp[$y][] = $cell;
            $x++;
        }
        return $temp ?? [];
    }

    /**
     * @param Game $game
     */
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

    /**
     * @param Game $game
     * @param Field $field
     */
    private function makeCells(Game $game, Field $field){
        $cells = [];
        $players = DB::table('players')->where('game_id', $game->getAttribute('id'))->get('id');

        for ($i = 0; $i < $field->getAttribute('height'); $i++){
            for ($j = 0; $j < $field->getAttribute('width'); $j++){

                $cell = new Cell();

                if ($i == 0 && $j == 0){
                    $cell->fill([
                        'color' => Player::where('id', $players[0]->id)->first()->color,
                        'playerId' => $players[0]->id
                    ]);
                }

                else if ($i + 1 == $field->getAttribute('width') && $j + 1 == $field->getAttribute('height')){
                    $cell->fill([
                        'color' => Player::where('id', $players[1]->id)->first()->color,
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
