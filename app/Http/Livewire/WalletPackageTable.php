<?php

namespace App\Http\Livewire;

use Excel;
use Illuminate\Support\Str;
use App\Exports\CustomExport;
use App\Models\WalletPackage;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class WalletPackageTable extends DataTableComponent
{

    protected $model = WalletPackage::class;
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
                'id' => 'walletpackage-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.walletpackages.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.walletpackages.destroy', $row->id);
                    $edit_route = route('admin.walletpackages.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-walletpackage-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.walletpackages.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('For new user', 'for_new_user')
                ->format(function ($value, $row, Column $column) {
                    if (!empty($value)) {
                        $status = ($value == 'yes') ? 'checked' : '';
                    } else {
                        return 'not found';
                    }
                    $route = route('admin.walletpackages.for-new-user');
                    $switch = '<div class="custom-control custom-control-success custom-switch">
                                <input data-block="tr" data-route="' . $route . '" value="' . $row->id . '" type="checkbox" ' . $status . '
                                    class="custom-control-input status-switch" id="for-new-user' . $row->id . '">
                                <label class="custom-control-label" for="for-new-user' . $row->id . '"></label>
                            </div>';
                    return $switch;
                })
                ->html(),
            Column::make('type')
                ->format(function ($value, $row, Column $column) {
                    return Str::ucfirst($value);
                }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),

            Column::make('Amount', 'amount')
                ->sortable()
                ->searchable(),
            Column::make('Bonus', 'bonus'),
            Column::make('Amount In Dollar', 'amount_dollar')
                ->sortable()
                ->searchable(),
            Column::make('Bonus In Dollar', 'bonus_dollar')
                ->sortable()
                ->searchable(),
            Column::make('Remark', 'remark')
                ->format(function ($value, $row, Column $column) {
                    $html = '<span data-hidden class="d-none">' . $value . '</span>';
                    return $html .= '<button data-remark class="btn btn-primary btn-sm">View</button>';
                })
                ->html()
                ->sortable()
                ->searchable(),



            // Column::make('image')
            // ->format(function ($row) {
            //     if ($row) {
            //         return '<img src="' . asset($row) . '" class="view-on-click  rounded-circle">';
            //     } else {
            //         return '<img src="' . asset('images/placeholder.jpg') . '" class="view-on-click  rounded-circle">';
            //     }
            // })
            // ->html(),

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
            //         $builder->where('walletpackages.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = WalletPackage::query();
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected()
    {
        $modelData = new WalletPackage;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'walletpackages.xlsx');
    }
}
