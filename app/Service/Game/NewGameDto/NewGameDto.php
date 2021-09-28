<?php

namespace App\Service\Game\NewGameDto;

class NewGameDto
{
    /** @var string */
    private $width;

    /** @var string */
    private $height;

    /**
     * @param $request
     * @return NewGameDto
     */
    public static function getDto($request): NewGameDto
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
