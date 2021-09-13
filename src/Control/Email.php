<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a email-validated field using <INPUT TYPE="EMAIL">
 */

class Email extends TextBox
{
    public function renderControl(array $extra_attributes = []): string
    {
        return parent::renderControl([
            'type' => 'email',
        ]);
    }
}