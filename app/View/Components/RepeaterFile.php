<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RepeaterFile extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $class;
    public $name;
    public $hasLabel;
    public $multiple;
    public $attrs;
    public $parentClass;
    public $id;
    public $repeaterName;
    public function __construct(
        $name,
        $repeaterName,
        $class = "",
        $hasLabel = true,
        $multiple = false,
        $attrs = "",
        $parentClass = "",
        $id = null
    ) {
        $this->repeaterName = $repeaterName;
        $this->class = $class;
        $this->name = $name;
        $this->hasLabel = $hasLabel;
        $this->multiple = $multiple;
        $this->attrs = $attrs;
        $this->parentClass = $parentClass;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.repeater-file');
    }
}
