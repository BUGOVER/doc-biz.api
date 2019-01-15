<?php
/** @noinspection StaticInvocationViaThisInspection */
declare(strict_types=1);

namespace App\Services;

use App\Models\DocumentPosition;
use App\Models\GroupPosition;
use LaraService\Services\LaraService;

/**
 * Class BaseService
 * @package App\Services
 */
abstract class BaseService extends LaraService
{
    /**
     * @var
     */
    protected $validationErrors;

    /**
     * @param $validator
     * @param $data
     * @param array $options
     * @return bool
     */
    public function validate($validator, $data, $options = [])
    {
        if ($validator->isValid($data, $options)) {
            return true;
        }

        $this->setValidationErrors($validator->getErrors());
        return false;
    }

    /**
     * @param $errors
     */
    public function setValidationErrors($errors)
    {
        $this->validationErrors = $errors;
    }

    /**
     * @return mixed
     */
    protected function getMaxCurrentPosition()
    {
        $groupPosition = new GroupPosition();
        $documentPosition = new DocumentPosition();

        $latestIndexGroup = $groupPosition
            ->where('user_id', '=', USER_ID)
            ->where('company_id', '=', COMPANY_ID)
            ->max('current_position');

        $latestIndexDocument = $documentPosition
            ->where('user_id', '=', USER_ID)
            ->where('company_id', '=', COMPANY_ID)
            ->max('current_position');

        return max($latestIndexGroup, $latestIndexDocument);
    }
}
