<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Editor extends Component
{
    /**
     * Create a new component instance.
     * @param string $name
     * @param string $label
     * @return void
     */
    public $name;
    public $label;
    public $id;
    public function __construct($name, $label = "", $id = null)
    {
        $this->name = $name;
        $this->label = $label;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.editor');
    }
}
