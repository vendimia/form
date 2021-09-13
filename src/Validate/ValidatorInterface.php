<?php
namespace Vendimia\Form\Validate;

interface ValidatorInterface
{
    public function validate($value): bool;
}