<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\{Tag, OptionTags};

/**
 * Renders a list with a <SELECT> and multiple <OPTION> tags
 */
class ListBox extends ControlAbstract implements ControlInterface
{
    protected $extra_properties = [
        // Element list
        'list' => [],
    ];

    public function renderControl(array $extra_attributes = []): string
    {
        $args = array_merge([
            'id' => $this->getId(),
            'name' => $this->name,
        ], $extra_attributes, $this->properties['html']);

        return Tag::select(...$args)->setContent(
            new OptionTags($this->properties['list'],
            [$this->element->getValue()]
        )
        )->noEscapeContent();
    }

    /**
     * Sets a value list for this control.
     */
    public function setList(array $value_list)
    {
        $this->properties['list'] = $value_list;
    }
}
