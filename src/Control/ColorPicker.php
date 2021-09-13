<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a color picker using <INPUT TYPE="COLOR">
 */
class ColorPicker extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'color',
        ]);
    }
}