<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a date picker field using <INPUT TYPE="DATE">
 */

class Date extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'date',
        ]);
    }
}