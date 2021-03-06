<?php

namespace App\Service\Game\NewGameDto;

use App\Exceptions\BusinessException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class NewGameRequest
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
    public function getDto(): NewGameDto
    {
        return NewGameDto::getDto($this->getRequest()) ;
    }

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function getRequest(): array
    {
        $validator = Validator::make($this->request->all(), [
                'width' => 'required|numeric|min:5|max:99|regex:"^\d*[02468]$"',
                'height' => 'required|numeric|min:5|max:99|regex:"^\d*[02468]$"',
            ]
        );
        if ($validator->fails()){
            throw new BusinessException('Ширина или высота не попадают в диапазоны минимального и максимального значения
             или четные', 400);
        }
        return $validator->validate();
    }
}
