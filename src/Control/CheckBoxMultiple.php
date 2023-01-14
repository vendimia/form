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
        'html_envelop_tag' => [],
    ];

    public function renderControl(array $extra_attributes = []): string
    {
        $html = [];

        foreach ($this->properties['list'] as $name => $description)
        {
            $args = array_merge([
                'type' => 'checkbox',
                'name' => $this->name . '[]',
                'value' => $name
            ], $extra_attributes, $this->getProperty('html'));

            $input_tag = Tag::input(...$args);

            if (in_array($name, $this->element->getValue())) {
                $input_tag['checked'] = 'true';
            }

            $checkbox = '<label>' .
                $input_tag .
                $description .
                '</label>'
            ;

            if($this->properties['html_envelop_tag']) {
                $checkbox =
                    (new Tag(...$this->properties['html_envelop_tag']))($checkbox)
                    ->noEscapeContent()
                ;
            }

            $html[] = $checkbox;
        }

        return join('', $html);
    }
}