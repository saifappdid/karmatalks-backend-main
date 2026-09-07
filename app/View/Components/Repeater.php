<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Repeater extends Component
{
    public $name;
    public $fields;
    public $maxLimit;
    public function __construct($name, $fields = "", $maxLimit = 0)
    {
        $this->name = $name;
        $this->fields = $fields;
        $this->maxLimit = $maxLimit;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.repeater');
    }
}
