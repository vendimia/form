<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a series of <INPUT TYPE="CHECKBOX"> tags
 */
class CheckBoxMultiple extends ControlAbstract implements ControlInterface
{
    protected $extra_properties = [
        // Element list
        'list' => [],

        // Array defining surrounding HTML element for each checkbox
        'html_checkbox_element' => [],
    ];

    public function renderControl(array $extra_attributes = []): string
    {
        $html = [];

        foreach ($this->properties['list'] as $name => $description)
        {
            $input_tag = Tag::input(
                type: 'checkbox',
                name: $this->name . '[]',
                value: $name
            );

            if (in_array($name, $this->element->getValue())) {
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