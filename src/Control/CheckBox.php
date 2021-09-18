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
        $params = [
            'id' => $this->getId(),
            'type' => 'checkbox',
            'name' => $this->name,
        ];
        if ($this->element->getValue()) {
            $params['checked'] = true;
        }

        $args = array_merge($params, $extra_attributes, $this->properties['html']);

        return Tag::input(...$args);
    }

}
