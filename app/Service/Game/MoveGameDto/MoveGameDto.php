<?php

namespace App\Service\Game\MoveGameDto;

class MoveGameDto
{
    /** @var string */
    private $gameId;

    /** @var integer */
    private $playerId;

    /** @var string */
    private $color;

    /**
     * @param $request
     * @return MoveGameDto
     */
    public static function getDto($request): MoveGameDto
    {
        $dto = new self;

        $dto->setColor($request['color']);
        $dto->setGameId($request['gameId']);
        $dto->setPlayerId($request['playerId']);

        return $dto;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getGameId(): string
    {
        return $this->gameId;
    }

    /**
     * @param string $gameId
     */
    public function setGameId(string $gameId): void
    {
        $this->gameId = $gameId;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    /**
     * @param int $playerId
     */
    public function setPlayerId(int $playerId): void
    {
        $this->playerId = $playerId;
    }

}
