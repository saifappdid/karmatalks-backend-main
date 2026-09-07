<?php

namespace App\Http\Livewire;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class BookingTable extends DataTableComponent
{

    protected $model = Booking::class;
    public $counter = 1;
    public $status;
    public $user;
    public function mount($user = null)
    {
        $this->user = $user;
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
                'id' => 'booking-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.bookings.index',
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
            Column::make('Booking UID', 'booking_uid')
                ->format(function ($value, $row, Column $column) {
                    return '<a href="' . route('admin.bookings.show', $row->id) . '" class="text-primary">' . $value . '</a>';
                })
                ->html()
                ->searchable()
                ->sortable(),
            Column::make('Status', 'status')
                ->format(function ($value, $row, Column $column) {
                    $statusClass = 'badge badge-light-secondary';
                    if ($value == 'started') {
                        $statusClass = 'badge badge-light-success';
                    } elseif ($value == 'ended') {
                        $statusClass = 'badge badge-light-danger';
                    }
                    return '<span class="' . $statusClass . '">' . ucfirst($value) . '</span>';
                })->html()
                ->searchable()
                ->sortable(),
            Column::make('User', 'user.name')
                ->searchable()
                ->sortable(),
            Column::make('Country', 'user.country.name')
                ->searchable()
                ->sortable(),
            Column::make('Astro', 'astro_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->astro ? $row->astro->first_name . ' ' . $row->astro->middle_name . ' ' . $row->astro->last_name : '-';
                })
                ->searchable()
                ->sortable(),
            Column::make('currency')->hideIf(true),
            Column::make('Amount', 'final_total')
                ->format(function ($value, $row, Column $column) {
                    return '<span class="badge badge-light-primary">' . ($row->currency == 'INR' ? '₹ ' : '$ ') . number_format($value, 2) . '</span>';
                })
                ->html()
                ->sortable(),
            Column::make('Session Date', 'session_date')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . date("M jS, Y", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('From Time', 'start_time')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . date("h:i A", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('To Time', 'end_time')
                ->format(function ($value) {
                    return '<span class="badge badge-light-success">' . date("h:i A", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('Service', 'booking_data')
                ->format(function ($value, $row, Column $column) {
                    if ($row->booking_data) {
                        $data = json_decode($row->booking_data, true);
                        // dd($data['service']['title']);
                        return isset($data['service']) ? $data['service']['title'] : '-';
                        // return $row->booking_data->service->name ?? '-';
                    }
                    return '-';
                })
                ->searchable()
                ->sortable(),
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
            //         $builder->where('bookings.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Booking::query();
        $modal->with(['user' => ['country'], 'astro']);
        if (in_array($this->status, ['confirmed', 'started', 'ended'])) {
            $modal->where('bookings.status', $this->status);
        }
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
        $modelData = new Booking;
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'bookings.xlsx');
    }
}
