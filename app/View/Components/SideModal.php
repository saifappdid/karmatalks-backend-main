<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SideModal extends Component
{
    public $id;
    public $title;
    public $class;
    public $modalWidth;
    public function __construct($id, $title = "", $class = "", $modalWidth = "")
    {
        $this->id = $id;
        $this->title = $title;
        $this->class = $class;
        $this->modalWidth = $modalWidth;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.side-modal');
    }
}
