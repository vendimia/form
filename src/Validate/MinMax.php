<?php
namespace Vendimia\Form\Validate;

use Attribute;

/**
 * Validate if a number has a minimum or maximun value.
 *
 * Error templates: min, max, invalid
 * Template variables: required, actual
 */
#[Attribute]
class MinMax extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'min' => 'Value must be greater or equal to {required}',
        'max' => 'Value must be less or equal to {required}',
        'invalid' => 'Value must be numeric'
    ];

    public function __construct(
        private ?float $min = null,
        private ?float $max = null,
        ?array $fail_templates = null,
    )
    {
        parent::__construct($fail_templates);
    }

    public function validate($value): bool
    {
        if (!is_numeric($value)) {
            $this->addMessage('invalid');
            return false;
        }

        $value = floatval($value);

        if ($this->min && $value < $this->min) {
            $this->addMessage('min', required: $this->min, actual: $value);
            return false;
        }

        if ($this->max && $value > $this->max) {
            $this->addMessage('max', required: $this->max, actual: $value);
            return false;
        }

        return true;
    }
}