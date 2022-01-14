<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Validate if a value is a valid date/time using strtotime()
 *
 * Error templates: invalid
 * Template variables: actual
 */
#[Attribute]
class DateTime extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'invalid' => 'Value "{actual}" is an invalid date/time',
    ];

    public function validate($value): bool
    {
        if (strtotime($value) !== false) {
            return true;
        }

        $this->addMessage('invalid', actual: $value);
        return false;
    }
}