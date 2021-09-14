<?php
namespace Vendimia\Form\Control;

use Vendimia\Form\Form;
use Vendimia\Form\Element;
use Vendimia\Html\Tag;

/**
 * ╭――――――――control_block―――――――――╮
 * │ ╭――――――caption_block―――――――╮ │
 * │ │     [label <label>]      │ │
 * │ │   [info_block (info)]    │ │
 * │ ╰――――――――――――――――――――――――――╯ │
 * │ ╭―――――――widget_block―――――――╮ │
 * │ |     [widget (widget)]    | │
 * │ |                          | │
 * │ | ╭―――――message_block――――╮ | │
 * │ | | [message (message1)] | | │
 * │ | | [message (message2)] | | │
 * │ | ╰――――――――――――――――――――――╯ | │
 * │ ╰――――――――――――――――――――――――――╯ │
 * ╰――――――――――――――――――――――――――――――╯
 */
abstract class ControlAbstract
{
    protected $properties = [

        // Prefix for IDs for various tags, including the form control per se.
        'id_prefix' => '',

        // Value for <LABEL> tag. Default is the control name with MB_CASE_TITLE.
        'caption' => null,

        // Text added to "info" block, if not null
        'info' => null,

        // Extra html attributes for the HTML control tag
        'html' => [],
    ];

    public function __construct(
        protected Form $form,
        protected Element $element,
        protected string $name,
        array $properties = []
    )
    {
        $this->properties = array_merge(
            $this->properties,
            $this->extra_properties ?? [],
            $properties
        );

        $this->properties['caption'] ??= mb_convert_case(strtr($name, '_', ' '),   MB_CASE_TITLE);
        $this->properties['id_prefix'] = mb_strtolower($this->form->getName());
    }

    /**
     * Returns the prefixed (if any) ID for a block in this control.
     */
    protected function getId($block = '')
    {
        $return = [
            $this->properties['id_prefix'],
            $this->name,
            $block
        ];

        return join('_', array_filter($return));
    }

    /**
     * Render the caption block
     */
    public function renderCaption(): string
    {
        $block = new Tag(...$this->form::$html_caption_block);

        $caption = $this->properties['caption'] . $this->form::$label_suffix;

        $content = Tag::label(for: $this->getId())->setContent($caption);

        if ($this->properties['info']) {
            $content .= (new Tag(...$this->form::$html_info_block))(
                $this->properties['info']
            );
        }

        $block->setContent($content)->noEscapeContent();
        return $block;
    }

    /**
     * Renders the message list
     */
    public function renderMessages(): string
    {
        $messages = '';
        foreach ($this->element->getMessages() as $message) {
            $tag = new Tag(...$this->form::$html_message);
            $tag->setContent($message);

            $messages .= $tag;
        }

        return $messages;
    }

    /**
     * Render the widget block of this control
     */
    public function renderWidget(array $extra_attributes = []): string
    {
        $control = $this->renderControl();

        // Creamos los mensajes
        $messages = new Tag(...$this->form::$html_message_block);
        $messages->setContent($this->renderMessages())
            ->noEscapeContent()
        ;

        $widget = new Tag(...$this->form::$html_widget_block);
        $widget->setContent($control . $messages)->noEscapeContent();

        return $widget;
    }

    /**
     * Render the complete control
     */
    public function render(): string
    {
        $caption = $this->renderCaption();
        $widget = $this->renderWidget();

        $control = new Tag(...$this->form::$html_control_block);
        $control->setContent($caption . $widget)->noEscapeContent();

        return $control;
    }
}
