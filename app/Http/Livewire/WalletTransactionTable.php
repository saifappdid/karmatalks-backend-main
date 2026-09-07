<?php

namespace App\Http\Livewire;

use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class WalletTransactionTable extends DataTableComponent
{

    protected $model = WalletTransaction::class;
    public $counter = 1;
    public $user;
    public function mount($user = null)
    {
        $this->user = $user;

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
                'id' => 'wallettransaction-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.wallettransactions.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [


            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),



            Column::make('User Name', 'user.name')
                ->sortable()
                ->searchable(),
            Column::make('User Mobile no.', 'user.phone')
                ->sortable()
                ->searchable(),

            Column::make('Currency', 'currency')
                ->format(function ($value) {
                    return $value;
                })
                ->sortable(),
            Column::make('Amount', 'amount')
                ->format(function ($value, $row, Column $column) {
                    return ($row->currency == 'INR' ? '₹ ' : '$ ') . number_format($value, 2);
                })
                ->sortable(),

            Column::make('Type', 'type')
                ->format(function ($value, $row, Column $column) {
                    $color = match (strtolower($value)) {
                        'credit'   => 'badge-success',
                        'debit'     => 'badge-danger',
                    };

                    return '<span class="badge ' . $color . '">' . ucfirst($value) . '</span>';
                })->html()
                ->sortable()
                ->searchable(),


            Column::make('Remark', 'remark')
                ->searchable(),





            Column::make('Created at', 'created_at')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),

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
            //         $builder->where('wallettransactions.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = WalletTransaction::query();
        $modal->with(['user']);
        if ($this->user) {
            $modal->where('user_id', $this->user);
        }
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected()
    {
        $modelData = new WalletTransaction;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'wallettransactions.xlsx');
    }
}
