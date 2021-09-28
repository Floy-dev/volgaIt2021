<?php

namespace App\Service\Game\MoveGameDto;

use App\Exceptions\BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MoveGameRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws ValidationException
     */
    public function getDto(): MoveGameDto
    {
        return MoveGameDto::getDto($this->getRequest()) ;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function getRequest(): array
    {
        $validator = Validator::make($this->request->all(), [
                'gameId' => 'required',
                'playerId' => 'required',
                'color' => 'required',
            ]
        );
        if ($validator->fails()){
            throw new BusinessException('Неправильные параметры запроса', 400);
        }
        return $validator->validate();
    }
}
