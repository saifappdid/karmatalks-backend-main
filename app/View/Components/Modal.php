<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $id;
    public $footer;
    public $size;
    public $title;
    public $animation;
    public $btn;
    public $btnText;
    public function __construct($id, $footer = true, $size = 'md', $title = '', $animation = 'fade', $btn = 'primary', $btnText = 'Close')
    {
        $this->id = $id;
        $this->footer = $footer;
        $this->size = $size;
        $this->title = $title;
        $this->animation = $animation;
        $this->btn = $btn;
        $this->btnText = $btnText;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
