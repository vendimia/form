<?php
namespace Vendimia\Form;

/**
 * Element with 'optional' property active.
 */
class OptionalElement extends Element
{
    public function __construct(...$args)
    {
        parent::__construct(...$args);
        $this->properties['optional'] = true;
    }
}