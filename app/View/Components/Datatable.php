<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Datatable extends Component
{
    public $class;
    public $columns;
    public $route;
    public $loadingText;




    public function __construct($class, $columns, $route, $loadingText = "Wait...")
    {
        $this->class = $class;
        $this->columns = $columns;
        $this->route = $route;
        $this->loadingText = $loadingText;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.datatable');
    }
}
