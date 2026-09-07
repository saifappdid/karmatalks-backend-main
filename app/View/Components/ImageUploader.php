<?php

namespace App\View\Components;

use Illuminate\Support\Collection;
use Illuminate\View\Component;

class ImageUploader extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $name;
    public $id;
    public $preview;
    public $onlyPreview;
    public $class;
    public $route;
    public $mainClass;
    public function __construct(string $name, $mainClass = null, string $id = null, array|Collection $preview = [], bool $onlyPreview = false, $class = 'col-md-4', $route = null)
    {
        $this->name = $name;
        $this->id = $id;
        $this->preview = $preview;
        $this->onlyPreview = $onlyPreview;
        $this->class = $class;
        $this->route = $route;
        $this->mainClass = $mainClass;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image-uploader');
    }
}
