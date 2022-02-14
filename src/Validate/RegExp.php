<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Validate if a matches the regular expression $regexp.
 *
 * The $regexp pattern will be automatically surrounded by '%' character.
 *
 * Error templates: invalid
 * Template variables: actual
 */
#[Attribute]
class RegExp extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'invalid' => 'Value "{actual}" is invalid',
    ];

    public function __construct(
        private string $regexp,
        ?array $fail_templates = null,
    )
    {
        parent::__construct($fail_templates);
    }

    public function validate($value): bool
    {
        $return = preg_match('%' . $this->regexp . '%u', $value);

        if ($return !== 1) {
            $this->addMessage('invalid', actual: $value);
            return false;
        }

        return true;
    }

}