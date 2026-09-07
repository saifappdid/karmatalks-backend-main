<?php

namespace App\Http\Livewire;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class OrderTable extends DataTableComponent
{

    protected $model = Order::class;
    public $counter = 1;
    public $status;
    public function mount()
    {
        $this->status = request()->segment(count(request()->segments()));
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
                'id' => 'order-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.orders.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            // Column::make('Actions')
            //     ->label(function ($row, Column $column) {
            //         $delete_route = route('admin.orders.destroy', $row->id);
            //         $edit_route = route('admin.orders.edit', $row->id);
            //         $edit_callback = 'setValue';
            //         $modal = '#edit-order-modal';
            //         return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
            //     }),
            // Column::make('status')
            //     ->format(function ($value, $data, Column $column) {
            //         $route = route('admin.orders.status');
            //         return view('content.table-component.switch', compact('data', 'route'));
            //     }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('Type', 'type')
                ->format(function ($value) {
                    if ($value == 'service') {
                        return '<span class="badge badge-light-info">' . ucfirst($value) . '</span>';
                    } elseif ($value == 'product') {
                        return '<span class="badge badge-light-success">' . ucfirst($value) . '</span>';
                    }
                })->html()->searchable()->sortable(),

            Column::make('Order ID', 'order_uid')
                ->format(function ($value, $data) {
                    return '<a href="' . route('admin.orders.show', $data->id) . '" class="text-primary">' . $value . '</a>';
                })->html()->searchable()->sortable(),
            Column::make('User Name', 'user.name')->searchable()->sortable(),
            Column::make('Status', 'status')
                ->format(function ($value) {
                    if ($value == 'delivered') {
                        return '<span class="badge badge-light-success">' . ucfirst($value) . '</span>';
                    } elseif ($value == 'cancelled') {
                        return '<span class="badge badge-light-danger">' . ucfirst($value) . '</span>';
                    } else {
                        return '<span class="badge badge-light-warning">' . ucfirst($value) . '</span>';
                    }
                })->html()->searchable()->sortable(),
            Column::make('currency')->hideIf(true),
            Column::make('Total Amount', 'total')
                ->format(function ($value, $row, Column $column) {
                    return '<span class="badge badge-light-primary">' . ($row->currency == 'INR' ? '₹ ' : '$ ') . number_format($value, 2) . '</span>';
                })->html()->searchable()->sortable(),
            Column::make('Payment Mode', 'payment_mode')->searchable()->sortable(),




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
            //         $builder->where('orders.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Order::query();
        $modal->with(['user', 'orderDetails']);
        if (in_array($this->status, ['confirmed', 'shipped', 'delivered'])) {
            $modal->where('orders.status', $this->status);
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
        $modelData = new Order;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'orders.xlsx');
    }
}
