<?php

namespace App\View\Components;

use Illuminate\View\Component;

class button extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $type;
    public $attrs;
    public $text;
    public $class;
    public $icon;
    public $isSubmit;
    public $id;
    public function __construct($id = "", $type = "primary", $attrs = "", $text = "", $class = "primary", $icon = "", $isSubmit = false)
    {
        $this->type = $type;
        $this->attrs = $attrs;
        $this->text = $text;
        $this->class = $class;
        $this->icon = $icon;
        $this->isSubmit = $isSubmit;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button');
    }
}
