<?php

namespace App\Service\Game\GameDto;

class GameDto
{
    /** @var string */
    private $id;

    /**
     * @param $data
     * @return GameDto
     */
    public static function getDto($data): GameDto
    {
        $dto = new self;

        $dto->setId($data['id']);

        return $dto;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

}
