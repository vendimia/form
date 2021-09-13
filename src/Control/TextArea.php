<?php
namespace Vendimia\Form\Control;

use Vendimia\Html\Tag;

/** 
 * Renders a text area editor using <TEXTAREA> tag
 */
class TextArea extends ControlAbstract implements ControlInterface
{
    public function renderControl(): string
    {
        $args = array_merge([
            "id" => $this->getId(),
            "name" => $this->name,
        ], $this->properties['html']);
        
        return Tag::textarea(...$args)($this->element->getValue());
    }
}