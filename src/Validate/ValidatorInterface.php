<?php
namespace Vendimia\Form\Validate;

use Vendimia\Form\Form;

interface ValidatorInterface
{
    public function setForm(Form $form): void;

    public function validate($value): bool;
}