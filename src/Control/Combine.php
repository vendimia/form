<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders multiple controls inside one
 */
class Combine extends ControlAbstract implements ControlInterface
{
    protected $extra_properties = [
        // Control list
        'controls' => [],

        // Separation HTML between controls
        'separator' => '',
    ];

    public function initialize()
    {
        foreach ($this->properties['controls'] as $c_name) {
            $this->form->$c_name->getControl()->setProperty('render', false);
        }
        $this->element->setProperty('validate', false);
    }

    public function renderControl(array $extra_attributes = []): string
    {
        $html = [];
        foreach ($this->properties['controls'] as $c_name) {
            $control = $this->form->$c_name->getControl();
            $html[] = $control->renderControl();
            $this->element->addMessage(...$control->element->getMessages());
        }

        return join($this->properties['separator'], $html);
    }
}