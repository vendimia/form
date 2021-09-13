<?php
namespace Vendimia\Form\Control;

interface ControlInterface
{
    /** 
     * Render the caption block
     */    
    public function renderCaption(): string;

    /** 
     * Render the widget block
     */
    public function renderWidget(array $extra_attributes = []): string;

    /** 
     * Renders the HTML form element
     */
    public function renderControl(): string;

}