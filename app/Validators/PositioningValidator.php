<?php
declare(strict_types=1);


namespace App\Validators;


use LaraValidation\CoreValidator;
use LaraValidation\LaraValidator;

/**
 * Class PositioningValidator
 * @package App\Validators
 */
class PositioningValidator extends LaraValidator
{
    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationPositioningGroup(): CoreValidator
    {
        $this->validator->required([
            'groupId',
            'groupName',
            'previousIndex',
            'newIndex'
        ]);

        return $this->validator;
    }

    /**
     * @return \LaraValidation\CoreValidator
     */
    public function validationPositioningDocument(): CoreValidator
    {
        $this->validator->required([
            'documentId',
            'documentName',
            'previousIndex',
            'newIndex'
        ]);

        return $this->validator;
    }
}
