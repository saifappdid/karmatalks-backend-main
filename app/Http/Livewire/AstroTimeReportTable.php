<?php

namespace App\Http\Livewire;

use App\Exports\AstroReportExport;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use App\Models\Astro;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class AstroTimeReportTable extends DataTableComponent
{

    protected $model = Astro::class;
    public $counter = 1;
    public $start_date;
    public $end_date;
    public function mount()
    {
        $this->dispatchBrowserEvent('table-refreshed');
        $this->start_date = request()->start_date;
        $this->end_date = request()->end_date;
    }
    
    public function configure(): void
    {
        $this->counter = 1;
        $this->setFilterPillsStatus(false);

        $this->setFiltersDisabled();
        // $this->setBulkActionsDisabled();
        $this->setColumnSelectDisabled();

        $this->setPrimaryKey('id')

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'astrotimereporttable-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.astrotimereporttables.index',
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


            Column::make('Name', 'first_name')
                ->format(function ($value, $row, Column $column) {
                    return '<span class="badge badge-light-primary">' . $value . '</span>';
                })
                ->html()
                ->searchable()
                ->sortable(),

            Column::make('Todays Time Spent (in hrs)', 'id')
                ->format(function ($value, $row, Column $column) {

                    $today = now()->toDateString();

                    $bookingDuration = $row->bookings()
                        ->whereDate('session_date', $today)
                        ->where('status', 'ended')
                        ->sum('duration');

                    $instantDuration = $row->instantConsultationHistories()
                        ->whereDate('created_at', $today)
                        ->where('status', 'ended')
                        ->sum('duration');

                    $totalMinutes = (int) $bookingDuration + (int) $instantDuration;
                    $hours = round($totalMinutes / 60, 2);  // or /60 if stored in minutes

                    return '<span class="badge badge-light-info">' . $hours . ' hrs</span>';
                })
                ->html()
                ->sortable(),
            Column::make('Todays Earned Amount', 'id')
                ->format(function ($value, $row, Column $column) {

                    $today = now()->toDateString();
                    $totalAmount = $row->instantConsultationHistories()
                        ->whereDate('created_at', $today)
                        ->where('status', 'ended')
                        ->sum('amount');

                    return '<span class="badge badge-light-info">' . $totalAmount . ' Rs</span>';
                })
                ->html()
                ->sortable(),

            Column::make('Monthly Time Spent (in hrs)', 'id')
                ->format(function ($value, $row, Column $column) {

                    $startOfMonth = now()->startOfMonth()->toDateString();
                    $endOfMonth = now()->endOfMonth()->toDateString();

                    $bookingDuration = $row->bookings()
                        ->whereBetween('session_date', [$startOfMonth, $endOfMonth])
                        ->where('status', 'ended')
                        ->sum('duration');

                    $instantDuration = $row->instantConsultationHistories()
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->where('status', 'ended')
                        ->sum('duration');

                    $totalMinutes = (int) $bookingDuration + (int) $instantDuration;
                    $hours = round($totalMinutes / 60, 2); // or /60 if stored in minutes

                    return '<span class="badge badge-light-warning">' . $hours . ' hrs</span>';
                })
                ->html()
                ->sortable(),

            Column::make('Monthly Earned Amount', 'id')
                ->format(function ($value, $row, Column $column) {

                    $startOfMonth = now()->startOfMonth()->toDateString();
                    $endOfMonth = now()->endOfMonth()->toDateString();
                    $totalAmount = $row->instantConsultationHistories()
                        ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                        ->where('status', 'ended')
                        ->sum('amount');

                    return '<span class="badge badge-light-warning">' . $totalAmount . ' Rs</span>';
                })
                ->html()
                ->sortable(),
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
            //         $builder->where('astrotimereporttables.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Astro::query();
        $modal->with(['bookings', 'instantConsultationHistories']);
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected()
    {
        $modelData = new Astro;
        return Excel::download(new AstroReportExport($this->getSelected(), $modelData, $this->start_date, $this->end_date), 'astro-report.xlsx');
    }
}
