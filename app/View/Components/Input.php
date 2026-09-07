<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
    public $class;
    public $name;
    public $type;
    public $hasLabel;
    public $attrs;
    public $parentClass;
    public $helperText;
    public $value;
    public $required;
    public $label;
    public $placeholder;
    public $id;
    public $maxLength;
    public $pattern;
    public function __construct(
        $class = "",
        $name = "",
        $type = "text",
        $hasLabel = true,
        $attrs = "",
        $parentClass = "",
        $helperText = "",
        $value = "",
        $required = true,
        $label = null,
        $placeholder = null,
        $id = null,
        $maxLength = null,
        $pattern = null,
    ) {
        $this->class = $class;
        $this->name = $name;
        $this->type = $type;
        $this->hasLabel = $hasLabel;
        $this->attrs = $attrs;
        $this->parentClass = $parentClass;
        $this->helperText = $helperText;
        $this->value = $value;
        $this->required = $required;
        $this->label = $label;
        $this->placeholder = $placeholder;
        $this->id = $id;
        $this->maxLength = $maxLength;
        $this->pattern = $pattern;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input');
    }
}
