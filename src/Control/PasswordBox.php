<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/**
 * Renders a text input control using <INPUT TYPE="PASSWORD"> tag
 */
class PasswordBox extends TextBox implements ControlInterface
{
    public function renderControl(array $extra_attributes = []): string
    {
        $extra_attributes['type'] = 'password';
        return parent::renderControl($extra_attributes);
    }
}