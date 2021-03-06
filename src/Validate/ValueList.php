<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Validate if a value is in the array $values
 *
 * Error templates: invalid
 * Template variables:
 */
#[Attribute]
class ValueList extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'invalid' => 'Value "{actual}" is invalid',
    ];

    public function __construct(
        private array $values,
        ?array $fail_templates = null,
    )
    {
        parent::__construct($fail_templates);
    }

    public function validate($value): bool
    {
        if (in_array($value, $this->values)) {
            return true;
        }

        $this->addMessage('invalid', actual: $value);
        return false;
    }

}