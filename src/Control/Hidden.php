<?php
namespace Vendimia\Form\Control;


/**
 * Renders a hidden field using <INPUT TYPE="HIDDEN">
 *
 * Note: This control should be render directly using
 * element->getControl()->renderWidger() to avoid rendering the label
 */
class Hidden extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'hidden',
        ]);
    }
}