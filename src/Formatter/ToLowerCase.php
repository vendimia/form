<?php
namespace Vendimia\Form\Formatter;

use Attribute;

/**
 * Converts the value to lowercase
 */
#[Attribute]
class ToLowerCase implements FormatterInterface
{
    public function format($value)
    {
        if(is_null($value)) {
            return null;
        }
        return mb_strtolower($value);
    }
}