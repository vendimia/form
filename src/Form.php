<?php
namespace Vendimia\Form;

use Vendimia\Http\Request;
use Vendimia\Html\Tag;
use Vendimia\Database\Entity;

use ReflectionObject;
use ReflectionProperty;
use ReflectionAttribute;
use Stringable;
use Traversable;

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

    // Validation state
    private ?bool $is_valid = null;


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

            // Precargamos formateadores y validadores
            foreach ($rp->getAttributes(
                Formatter\FormatterInterface::class,
                ReflectionAttribute::IS_INSTANCEOF
            ) as $ra) {
                $this->$name->addFormatter($ra->newInstance());
            }

            foreach ($rp->getAttributes(
                Validate\ValidatorInterface::class,
                ReflectionAttribute::IS_INSTANCEOF
            ) as $ra) {
                $this->$name->addValidator($validator = $ra->newInstance());
                $validator->setForm($this);
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
        if (!is_null($this->is_valid)) {
            return $this->is_valid;
        }

        $valid = true;

        // Ejecutamos el validador global, si es que hay
        if ($validator = static::$global_validator ?? null) {
            if (!$this->$validator()) {
                $valid = false;
            }
        }

        foreach ($this->elements as $element)
        {
            if (!$element->getProperty('enabled')) {
                continue;
            }

            if (!$element->getProperty('validate')) {
                continue;
            }

            if(!$element->validate()) {
                $valid = false;
            }

            // Si tiene mensajes, los jalamos
            if ($messages = $element->getMessages()) {
                $this->messages[$element->getName()] = $messages;
            }
        }


        $this->is_valid = $valid;

        return $valid;
    }

    /**
     * Alias of self::validate()
     */
    public function isValid(): bool
    {
        return $this->validate();
    }

    /**
     * Alias of !self::validate()
     */
    public function notValid(): bool
    {
        return !$this->validate();
    }


    /**
     * Return this form's name
     */
    public function getName()
    {
        return $this::$name;
    }

    /**
     * Sets values to all elements.
     *
     * This method also resets the validation information
     */
    public function setValues(array|Traversable|Entity $values)
    {
        // Si los valores vienen de un entity, lo convertimos a array
        if ($values instanceof Entity) {
            $values = $values->asArray();
        }

        foreach ($values as $element => $value)
        {
            // Si el valor es un Entity, obtenemos su pk()
            if ($value instanceof Entity) {
                $value = $value->pk();
            }

            // Solo añadimos si existe
            if (isset($this->$element)) {
                $this->$element->setValue($value);
            }
        }

        $this->is_valid = null;
        $this->messages = [];
    }

    /**
     * Adds a raw message for a element
     */
    public function addMessage($element, $message)
    {
        $this->messages[$element][] = $message;
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
     * Renders some or all element with a HTML control defined
     */
    public function render($elements = []): string
    {
        $source = $this->elements;

        if ($elements) {
            $source = array_intersect_key($this->elements, array_flip($elements));
        }
        $html = '';
        foreach ($source as $element)
        {
            if (!$element->getProperty('enabled')) {
                continue;
            }

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

        if ($submit) {
            $html .= Tag::button(type: 'submit')
            ->addAttributes(...$submit_attr)
            ->setContent($submit)
            ->noEscapeContent()
        ;
        }

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