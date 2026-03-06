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

        // Array defining surrounding HTML element for each radio button
        'html_envelop_tag' => [],

        // Array defining surrounding HTML element for each <INPUT TYPE=RADIO>
        'html_envelop_radio_tag' => [],

        // Array defining surrounding HTML element for each description
        'html_envelop_description_tag' => [],
    ];

    public function renderControl(array $extra_attributes = []): string
    {
        $html = [];

        foreach ($this->properties['list'] as $name => $description)
        {
            $args = array_merge([
                'type' => 'radio',
                'name' => $this->name,
                'value' => $name,
            ], $extra_attributes, $this->getProperty('html'));

            $input_tag = Tag::input(...$args);
            if ($name == $this->element->getValue()) {
                $input_tag['checked'] = 'true';
            }

            if ($this->properties['html_envelop_radio_tag']) {
                $input_tag = (new Tag(...$this->properties['html_envelop_radio_tag']))($input_tag)
                    ->noEscapeContent()
                ;
            }
            if ($this->properties['html_envelop_description_tag']) {
                $description = (new Tag(...$this->properties['html_envelop_description_tag']))($description)
                    ->noEscapeContent()
                ;
            }

            $radio = '<label>' .
                $input_tag .
                $description .
                '</label>'
            ;

            if($this->properties['html_envelop_tag']) {
                $radio =
                    (new Tag(...$this->properties['html_envelop_tag']))($radio)
                    ->noEscapeContent()
                ;
            }

            $html[] = $radio;
        }

        return join('', $html);
    }
}
