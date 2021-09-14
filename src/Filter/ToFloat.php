<?php
namespace Vendimia\Form\Filter;

use Attribute;

/**
 * Convers the value to a float number
 */
 #[Attribute]
class ToFloat implements FilterInterface
{
    public function filter($value)
    {
        if(is_null($value)) {
            return null;
        }
        return floatval($value);
    }
}
