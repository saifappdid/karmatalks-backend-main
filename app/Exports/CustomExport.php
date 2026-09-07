<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $selectedIds = [];
    public $modelData;
    public function __construct($selectedIds, $modelData)
    {
        $this->modelData = $modelData;
        $this->selectedIds = $selectedIds;
    }

    public function view(): View
    {
        $tableData = $this->modelData::whereIn('id', $this->selectedIds)->get();
        $columns = array_keys($tableData[0]->getAttributes());
        return view('content.exports.custom-export', [
            'tableData' => $tableData,
            'columns' => $columns,
        ]);
    }
}
