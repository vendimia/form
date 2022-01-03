<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Validate if a string is a valid email, based on https://html.spec.whatwg.org/multipage/input.html#valid-e-mail-address
 *
 * Error templates: min, max
 * Template variables: actual
 */
#[Attribute]
class Email extends ValidatorAbstract implements ValidatorInterface
{
    const EMAIL_REGEXP = '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/';

    protected array $fail_templates = [
        'invalid' => 'The email address {actual} failed validation',
    ];

    public function validate($value): bool
    {
        $valid = preg_match(self::EMAIL_REGEXP, $value) === 1;
        if (!$valid) {
            $this->addMessage('invalid', actual: $value);
        }

        return $valid;
    }
}
