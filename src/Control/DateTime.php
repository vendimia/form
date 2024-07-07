<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a date/time picker field using <INPUT TYPE="DATETIME-LOCAL">
 */
class DateTime extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'datetime-local',
        ]);
    }
}