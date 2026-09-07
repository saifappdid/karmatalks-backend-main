<?php

namespace App\View\Components;

use Illuminate\View\Component;

class RepeaterInput extends Component
{
    public $name;
    public $type;
    public $required;
    public $repeaterName;
    public function __construct($name, $repeaterName, $type = "text", $required = true)
    {
        $this->name = $name;
        $this->repeaterName = $repeaterName;
        $this->type = $type;
        $this->required = $required;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.repeater-input');
    }
}
