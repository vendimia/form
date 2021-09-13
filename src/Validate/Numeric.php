<?php
namespace Vendimia\Form\Validate;

use Attribute;

/** 
 * Only accepts numeric values.
 * 
 * Error templates: fail
 * Template variables: 
 */
#[Attribute]
class Numeric extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'fail' => 'Value must be a number',
    ];

    public function validate($value): bool
    {
        if (is_numeric($value)) {
            return true;
        }

        $this->addMessage('fail');
        return false;
    }

}