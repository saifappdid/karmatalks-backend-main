<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Card extends Component
{
    public $class;
    public $id;
    public $title;

    public function __construct($class = "", $id = "card", $title = null)
    {
        $this->class = $class;
        $this->id = $id;
        $this->title = $title;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.card');
    }
}
