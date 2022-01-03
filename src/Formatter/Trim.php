<?php
namespace Vendimia\Form\Formatter;

use Attribute;

/**
 * Removes spaces and other characters from any (or both) sides of a string
 */
#[Attribute]
class Trim implements FormatterInterface
{
    public function __construct(
        private $side = 'both',
        private $characters = ' ',
    )
    {

    }

    public function format($value)
    {
        if(is_null($value)) {
            return null;
        }

        if ($this->side == 'left') {
            return ltrim($value, $this->characters);
        } elseif ($this->side == 'right') {
            return rtrim($value, $this->characters);
        }

        return trim($value, $this->characters);
    }
}