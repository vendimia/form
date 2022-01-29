<?php
namespace Vendimia\Form\Formatter;

use Attribute;

/**
 * Convers the value to a boolean value
 */
 #[Attribute]
class ToBoolean implements FormatterInterface
{
    public function format($value)
    {
        if(is_null($value)) {
            return null;
        }
        return boolval($value);
    }
}
