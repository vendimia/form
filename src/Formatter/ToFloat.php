<?php
namespace Vendimia\Form\Formatter;

use Attribute;

/**
 * Convers the value to a float number
 */
 #[Attribute]
class ToFloat implements FormatterInterface
{
    public function format($value)
    {
        if(is_null($value)) {
            return null;
        }
        return floatval($value);
    }
}
