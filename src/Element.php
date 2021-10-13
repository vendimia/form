<?php
namespace Vendimia\Form;

use Vendimia\Form\Formatter\FormatterInterface;
use Vendimia\Form\Validate\ValidatorInterface;
use Vendimia\Form\Control\ControlInterface;

use RuntimeException;
use Stringable;

/**
 * Definition of each form element, for validating and/or for draw its HTML control
 */
class Element implements Stringable
{
    // HTML control
    private ?ControlInterface $control = null;

    // Value formatters
    private $formatters = [];

    // Value validators
    private $validators = [];

    // Error messages from validation
    private $messages = [];

    // Default value
    private $value;

    // General properties
    protected $properties = [
        // True if this element is included in form::getValues()
        'return_value' => true,

        // Name used in form::getValues(). Null uses this element name
        'field_name' => null,

        // True if an empty value is valid
        'optional' => false,

        // Message when a non-optional element is empty
        'required_message' => 'This element cannot be empty',

    ];

    /** Validation result, for reusing */
    private $is_valid = null;

    public function __construct(
        protected Form $form,
        protected string $name,
        array $properties,
    ) {

        $this->properties = array_merge($this->properties, $properties);
    }

    /**
     * Sets the HTML control for this element
     */
    public function setControl(ControlInterface $control)
    {
        $this->control = $control;
    }

    /**
     * Returns whether this element has a HTML control
     */
    public function hasControl(): bool
    {
        return !is_null($this->control);
    }

    /**
     * Returns this element HTML control
     */
    public function getControl(): ?ControlInterface
    {
        return $this->control;
    }

    /**
     * Adds a formatters to this element
     */
    public function addFormatter(FormatterInterface $formatters)
    {
        $this->formatters[] = $formatters;
    }

    /**
     * Adds a validator to this element
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }


    /**
     * Set a value to this element, applying all the filters
     */
    public function setValue($value)
    {
        // No podemos añadir NULL
        if (is_null($value)) {
            $value = '';
        }

        foreach ($this->formatters as $formatter) {
            $value = $formatter->format($value);
        }

        $this->is_valid = null;
        $this->value = $value;
    }

    /**
     * Returns this element value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return this element name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return this field name, if return_value = true, otherwise returns null
     */
    public function getFieldName(): ?string
    {
        if ($this->properties['return_value']) {
            return $this->properties['field_name'] ?? $this->name;
        }

        return null;
    }

    /**
     * Adds one or more messages to this element
     */
    public function addMessages(...$messages): void
    {
        $this->messages = array_merge($this->messages, $messages);
    }

    /**
     * Returns the message list generated in validation
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Validate this element value
     */
    public function validate(): bool
    {
        if (!is_null($this->is_valid)) {
            return $this->is_valid;
        }

        $optional = $this->properties['optional'];
        $empty = !(bool)$this->value;

        // Si no es opcional y está vacío, fallamos
        if (!$optional && $empty) {
            $this->addMessages($this->properties['required_message']);

            return $this->is_valid = false;
        }

        $valid = true;

        // Solo validamos si no está vacío
        if (!$empty) foreach ($this->validators as $validator)
        {
            if (!$validator->validate($this->value)) {
                $valid = false;
            }
            $this->addMessages(...$validator->getMessages());
        }

        return $this->is_valid = $valid;
    }

    public function getProperties(): array
    {
        return $this->properties;
    }


    /**
     * If this element has an HTML control, returns the rendered HTML element.
     * Otherwise returns its value.
     */
    public function __toString()
    {
        if ($this->hasControl()) {
            return $this->control->render();
        }
        return $this->value;
    }
}
