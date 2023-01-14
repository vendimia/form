<?php

namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a text input control using <INPUT TYPE="TEXT"> tag
 */
class File extends ControlAbstract implements ControlInterface
{
    protected $extra_properties = [
        // File types this control should accept, separated by commas
        'accept' => '',
    ];

    public function renderControl(array $extra_attributes = []): string
    {

        $args = array_merge([
            'id' => $this->getId(),
            'type' => 'file',
            'name' => $this->name,
            'value' => $this->element->getValue(),
            'accept' => $this->properties['accept'],
        ], $extra_attributes, $this->properties['html']);

        return Tag::input(...$args);
    }
}