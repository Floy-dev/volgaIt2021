<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ResponseStatusTrait
{
    protected function success(array $data = [], $code = 200)
    {
        return $this->responseWithStatus(true, $data, $code);
    }

    protected function failed(array $data = [], $code = 200)
    {
        return $this->responseWithStatus(false, $data, $code);
    }

    protected function errors(array $data = [], $code = 200)
    {
        return $this->failed(['errors' => $data], $code);
    }

    protected function responseWithStatus($success, array $data = [], $code = 200)
    {
        $data = array_merge(['success' => $success], $data);

        return new JsonResponse($data, $code);
    }
}
