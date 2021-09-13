<?php
namespace Vendimia\Form;

use Vendimia\Http\Request;
use Vendimia\Html\Tag;
use Vendimia\Database\Entity;

use ReflectionObject;
use ReflectionProperty;
use ReflectionAttribute;
use Stringable;

class Form implements Stringable
{
    public static $html_caption_block = ['div'];
    public static $html_info_block  = ['div', ['class' => 'vendimia-form-info']];
    public static $html_widget_block = ['div'];
    public static $html_message_block = ['ul', ['class' => 'vendimia-form-messages']];
    public static $html_message = ['li'];
    public static $html_control_block = ['div'];
    public static $label_suffix = ':';

    // Name of this form. Default is the class name
    public static $name = null;

    private array $elements = [];
    private array $messages = [];


    public function __construct(
        private ?Request $request = null,
    )
    {
        // El nombre de este formulario será el nombre de la clase, en minúsculas
        $this::$name ??= mb_strtolower(array_slice(explode('\\', $this::class), -1)[0]);
        
        $ro = new ReflectionObject($this);

        foreach ($ro->getProperties(ReflectionProperty::IS_PUBLIC) as $rp) {
            // Ignoramos las propiedades estáticas
            if ($rp->isStatic()) {
                continue;
            }
            $name = $rp->getName();

            $properties = [];

            // Si existe un atributo Element, lo usamos para sacar las propiedades
            $element = $rp->getAttributes(
                Element::class, 
                ReflectionAttribute::IS_INSTANCEOF
            )[0] ?? false;

            $element_class = Element::class;
            if ($element) {
                $properties = $element->getArguments();
                $element_class = $element->getName(); 
            }

            // Valor por defecto
            $value = $rp->getValue($this);

            // No permitimos valores nulos
            if (is_null($value)) {
                $value = '';
            }
            
            $this->$name = new $element_class(
                form: $this,
                name: $name,
                properties: $properties,
            );

            // Si existe un atributo Control, lo instanciamos
            $control = $rp->getAttributes(
                Control\ControlInterface::class,
                ReflectionAttribute::IS_INSTANCEOF
            )[0] ?? false;

            if ($control) {
                $this->$name->setControl(new ($control->getName())(
                    form: $this,
                    element: $this->$name,
                    name: $name,
                    properties: $control->getArguments(),
                ));
                
            }

            // Precargamos filtros y validadores
            foreach ($rp->getAttributes(
                Filter\FilterInterface::class, 
                ReflectionAttribute::IS_INSTANCEOF
            ) as $ra) {
                $this->$name->addFilter($ra->newInstance());
            }

            foreach ($rp->getAttributes(
                Validate\ValidatorInterface::class, 
                ReflectionAttribute::IS_INSTANCEOF
            ) as $ra) {
                $this->$name->addValidator($ra->newInstance());
            }
            
            $this->$name->setValue($value);

            $this->elements[$name] = $this->$name;
        }
    }

    /** 
     * Validates all form elements
     */
    public function validate(): bool
    {
        $valid = true;
        foreach ($this->elements as $element) 
        {
            if(!$element->validate()) {
                $valid = false;
                $this->messages[$element->getName()] = $element->getMessages();
            }
        }

        return $valid;
    }

    /**
     * Return this form's name
     */
    public function getName()
    {
        return $this::$name;
    }

    /** 
     * Sets values to elements
     */
    public function setValues(array|Traversable|Entity $values)
    {
        if ($values instanceof Entity) {
            $values = $values->asArray();
        }
        
        foreach ($values as $element => $value)
        {
            // Solo añadimos si existe
            if (isset($this->$element)) {
                $this->$element->setValue($value);
            }
        }
    }

    /**
     * Return all validation error messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /** 
     * Returns all elements values
     */
    public function getValues(): array
    {
        $return = [];

        foreach ($this->elements as $element)
        {
            if ($name = $element->getFieldName()) {
                $return[$name] = $element->getValue();
            }
        }

        return $return;
    }

    /** 
     * Renders all element with a HTML control defined
     */
    public function render(): string
    {
        $html = '';
        foreach ($this->elements as $element)
        {
            if ($element->hasControl()) {
                $html .= $element->getControl()->render();
            }
        }

        return $html;
    }

    /** 
     * Renders the form with <FORM> and <BUTTON TYPE=SUBMIT> tags
     */
    public function renderFull(
        $form_id = null,
        $method = 'post', 
        $submit = "Submit",
        $form_attr = [],
        $submit_attr = [],
    ): string
    {
        if (is_null($form_id)) {
            $form_id = 'form_' . self::$name;
        }

        $html = $this->render();
        $html .= Tag::button(type: 'submit')
            ->addAttributes(...$submit_attr)
            ->setContent($submit)
            ->noEscapeContent()
        ;
        
        $html = Tag::form(
            id: $form_id,
            method: $method,
        )
            ->addAttributes(...$form_attr)
            ->setContent($html)
            ->noEscapeContent()
        ;

        return $html;
    }

    /** 
     * Shortcut to renderFull()
     */
    public function __toString()
    {
        return $this->renderFull();
    }

    /**
     * Shorcuto to renderFull, for adding arguments
     */
    public function __invoke(...$args): string
    {
        return $this->renderFull(...$args);
    }
}