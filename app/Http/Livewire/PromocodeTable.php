<?php

namespace App\Http\Livewire;

use Excel;
use App\Models\Promocode;
use Illuminate\Support\Str;
use App\Exports\CustomExport;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class PromocodeTable extends DataTableComponent
{

    protected $model = Promocode::class;
    public $counter = 1;
    public function mount()
    {
        $this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        $this->counter = 1;
        $this->setFilterPillsStatus(false);

        $this->setFiltersDisabled();
        $this->setBulkActionsDisabled();
        $this->setColumnSelectDisabled();

        $this->setPrimaryKey('id')

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'promocode-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.promocodes.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.promocodes.destroy', $row->id);
                    $edit_route = route('admin.promocodes.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-promocode-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.promocodes.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),

            Column::make('code')->searchable(),
            Column::make('start')
                ->format(function ($value, $row, Column $column) {
                    return '<span class="badge badge-light-success">' . date("M jS, Y", strtotime($value)) . '</span>';
                })->html(),
            Column::make('end')
                ->format(function ($value, $row, Column $column) {
                    return '<span class="badge badge-light-danger">' . date("M jS, Y", strtotime($value)) . '</span>';
                })->html(),
            Column::make('discount')->searchable(),
            Column::make('Discount Type', 'discount_type')->format(function ($value, $row, Column $column) {
                return Str::ucfirst($value);
            }),
            Column::make('Minimum Order Amount', 'minimum_order_amount'),
            Column::make('Max Discount Amount', 'max_discount_amount'),
            Column::make('message'),
            Column::make('Created at', 'created_at')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            // Column::make('Updated at', 'updated_at')
            //     ->format(function ($value) {
            //        return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';

            //     })
            //     ->html()
            //     ->collapseOnTablet()
            //     ->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    'active' => 'Active',
                    'blocked' => 'Blocked',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('status', $value);
                }),

            // TextFilter::make('Name')
            //     ->config([
            //         'placeholder' => 'Search Name',
            //         'maxlength' => '25',
            //     ])
            //     ->filter(function (Builder $builder, string $value) {
            //         $builder->where('promocodes.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Promocode::query();
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected()
    {
        $modelData = new Promocode;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'promocodes.xlsx');
    }
}
