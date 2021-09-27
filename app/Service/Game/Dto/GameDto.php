<?php

namespace App\Service\Game\Dto;

class GameDto
{
    /** @var string */
    private $width;

    /** @var string */
    private $height;

    /**
     * @param $request
     * @return GameDto
     */
    public static function getDto($request): GameDto
    {
        $dto = new self;

        $dto->setWidth($request['width']);
        $dto->setHeight($request['height']);

        return $dto;
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     */
    public function setWidth(string $width): void
    {
        $this->width = $width;
    }

    /**
     * @return string
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * @param string $height
     */
    public function setHeight(string $height): void
    {
        $this->height = $height;
    }

}
