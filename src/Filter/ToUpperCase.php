<?php
namespace Vendimia\Form\Filter;

use Attribute;

/**
 * Converts the value to uppercase
 */
#[Attribute]
class ToUpperCase implements FilterInterface
{
    public function filter($value)
    {
        if(is_null($value)) {
            return null;
        }
        return mb_strtoupper($value);
    }
}