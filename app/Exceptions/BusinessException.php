<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use RuntimeException;
use Throwable;

class BusinessException extends \Exception
{
    /** @var array */
    private $errors;

    public function __construct($message = "", $code = 200, array $errors = [], Throwable $previous = null)
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);

        $this->message = $message;
        $this->code = $code;
    }

    public function render(): Response
    {
        return new Response($this->message, $this->code);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}
