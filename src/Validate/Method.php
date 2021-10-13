<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Execute a method for validate a value. A value other than 'true' will be
 * added as message, and invalidate the value.
 *
 * Error templates: fail
 * Template variables: message
 */
#[Attribute]
class Method extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'fail' => '{message}',
    ];

    public function __construct(private $method, ?array $fail_templates = null)
    {
        parent::__construct($fail_templates);
    }

    public function validate($value): bool
    {
        $return = $this->form->{$this->method}($value);

        if ($return !== true) {
            $this->addMessage('fail', message: $return);
            return false;
        }

        return true;
    }
}