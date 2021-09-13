<?php
namespace Vendimia\Form\Validate;

abstract class ValidatorAbstract implements ValidatorInterface
{
    protected array $messages = [];
    protected array $fail_templates = [];

    public function __construct(?array $fail_templates = null) 
    {
        if ($fail_templates) {
            $this->fail_templates = $fail_templates;
        }
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    /** 
     * Adds a message, replacing variables
     */
    public function addMessage($code, ...$args)
    {
        $message = $this->fail_templates[$code] ?? null;

        if (!$message) {
            $this->messages[] = "Validation '" . $this::class . "' failed, and error message '" . $code . "' is not available.";
            return;
        }

        $translate = [];
        foreach ($args as $name => $value) {
            $translate['{' . $name . '}'] = $value;
        }

        $this->messages[] = strtr($message, $translate);

    }
}
