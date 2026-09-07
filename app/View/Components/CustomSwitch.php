<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CustomSwitch extends Component
{
    public $parentClass;
    public $label;
    public $disabled;
    public $checked;
    public $class;
    public $id;
    public $name;
    public $syncTo;
    public function __construct($id, $name = "", $syncTo = "", $parentClass = "", $label = "", $disabled = false, $checked = false, $class = "")
    {
        $this->parentClass = $parentClass;
        $this->label = $label;
        $this->disabled = $disabled;
        $this->checked = $checked;
        $this->class = $class;
        $this->id = $id;
        $this->name = $name;
        $this->syncTo = $syncTo;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.custom-switch');
    }
}
