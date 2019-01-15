<?php
declare(strict_types=1);

namespace App\Http\Controllers;

/**
 * Class BaseController
 * @package App\Http\Controllers
 */
class BaseController extends Controller
{
    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @param null $service
     * @return mixed
     */
    public function response(array $data = [], int $status = 200, array $headers = [], bool $service = null)
    {
        if (null === $service) {
            $service = $this->baseService;
        }

        if ($service->getValidationErrors()) {
            $data = [
                'message' => 'The given data was invalid',
                'errors' => $service->getValidationErrors()
            ];
            $status = 422;
        }

        return response($data, $status)->withHeaders($headers);
    }
}
