<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a text input control using <INPUT TYPE="CHECKBOX"> tag
 */
class CheckBox extends ControlAbstract implements ControlInterface
{
    public function renderControl(array $extra_attributes = []): string
    {
        $args = array_merge([
            'id' => $this->getId(),
            'type' => 'checkbox',
            'name' => $this->name,
            'value' => $this->element->getValue(),
        ], $extra_attributes, $this->properties['html']);

        return Tag::input(...$args);
    }

}
