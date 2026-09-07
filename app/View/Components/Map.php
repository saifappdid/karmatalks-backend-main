<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Map extends Component
{
    public $linked_to;
    public $modal;
    public function __construct($linked_to = "#location", $modal = false)
    {
        $this->linked_to = $linked_to;
        $this->modal = $modal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.map');
    }
}
