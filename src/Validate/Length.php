<?php
namespace Vendimia\Form\Validate;

use Attribute;

/** 
 * Validate the length of a string.
 * 
 * Error templates: min, max
 * Template variables: required, actual
 */
#[Attribute]
class Length extends ValidatorAbstract implements ValidatorInterface
{
    protected array $fail_templates = [
        'min' => 'Value must have at least {required} characteres',
        'max' => 'Value must have no more than {required} characteres',
    ];

    public function __construct(
        private ?int $min = null,
        private ?int $max = null,
        ?array $fail_templates = null,
    )
    {
        parent::__construct($fail_templates);
    }

    public function validate($value): bool
    {
        $len = mb_strlen($value);

        if ($this->min && $len < $this->min) {
            $this->addMessage('min', required: $this->min, actual: $len);
            return false;
        }
        if ($this->max && $len > $this->max) {
            $this->addMessage('max', required: $this->max, actual: $len);
            return false;
        }

        return true;
    }

}