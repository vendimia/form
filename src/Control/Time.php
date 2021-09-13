<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a time picker control using <INPUT TYPE="TIME">
 */

class Time extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'time',
        ]);
    }
}