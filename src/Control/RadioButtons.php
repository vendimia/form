<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a series of <INPUT TYPE="CHECKBOX"> tags
 */
class RadioButtons extends ControlAbstract implements ControlInterface
{
    protected $extra_properties = [
        // Element list
        'list' => [],
    ];

    public function renderControl(array $extra_attributes = []): string
    {
        $html = [];

        foreach ($this->properties['list'] as $name => $description)
        {
            $input_tag = Tag::input(
                type: 'radio',
                name: $this->name,
                value: $name
            );
            if ($name == $this->element->getValue()) {
                $input_tag['checked'] = 'true';
            }

            $html[] = '<label>' .
                $input_tag .
                $description .
                '</label>'
            ;
        }

        return join('', $html);
    }
}