<?php
namespace Vendimia\Form\Formatter;

use Attribute;

/**
 * Converts the value to uppercase
 */
#[Attribute]
class ToUpperCase implements FormatterInterface
{
    public function format($value)
    {
        if(is_null($value)) {
            return null;
        }
        return mb_strtoupper($value);
    }
}