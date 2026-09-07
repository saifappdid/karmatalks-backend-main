<?php

namespace App\Http\Livewire;

use Excel;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exports\CustomExport;
use App\Exports\InstantConsultationExport;
use Illuminate\Database\Eloquent\Builder;
use App\Models\InstantConsultationHistory;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;


class InstantConsultationHistoryTable extends DataTableComponent
{

    protected $model = InstantConsultationHistory::class;
    public $counter = 1;
    public $start_date;
    public $end_date;
    public $user;
    public $type;
    public function mount($user = null)
    {
        $this->user = $user;
        $this->type = request()->type;
        $this->start_date = request()->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $this->end_date = request()->end_date ??   Carbon::now()->toDateString();
        $this->dispatchBrowserEvent('table-refreshed');
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
                'id' => 'instantconsultationhistory-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                // 'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.instant-consultation.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            // Column::make('Actions')
            //     ->label(function ($row, Column $column) {
            //         $delete_route = route('admin.instant-consultation.destroy', $row->id);
            //         $edit_route = route('admin.instant-consultation.edit', $row->id);
            //         $edit_callback = 'setValue';
            //         $modal = '#edit-instantconsultationhistory-modal';
            //         return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
            //     }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    return '<span class="badge badge-light-info">' . Str::ucfirst($value) . '</span>';
                })->html(),
            Column::make('User', 'user_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->user->name ?? '-';
                }),
            Column::make('Astro', 'astro_id')
                ->format(function ($value, $row, Column $column) {
                    return $row->astro->first_name . ' ' . $row->astro->middle_name . ' ' . $row->astro->last_name;
                }),
            Column::make('currency')->hideIf(true),
            Column::make('Country', 'user.country.name')
                ->searchable()
                ->sortable(),
            Column::make('amount')
                ->format(function ($value, $row, Column $column) {
                    return ($row->currency == 'INR' ? '₹ ' : '$ ') . number_format($value, 2);
                }),
            Column::make('Session Type', 'session_type')
                ->format(function ($value, $data, Column $column) {
                    return '<span class="badge badge-light-primary">' . Str::ucfirst($value) . '</span>';
                })->html(),
            Column::make('Chat History', 'id')
                ->format(function ($value, $data, Column $column) {
                    // return '<a href="' . route('admin.instant-consultation.chat', $data->id) . '" class="btn btn-sm btn-primary">View Chat</a>';
                    if ($data->session_type !== 'chat') {
                        return '';
                    }
                    return '<a href="' . route('admin.instant-consultation.chat', $data->id) . '" class="btn btn-sm btn-primary">View Chat</a>';
                })->html(),
            Column::make('Started at', 'started_at')
                ->format(function ($value) {
                    return '<span class="badge badge-light-info">' . date("M jS, Y h:i A", strtotime($value)) . '</span>';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('Ended at', 'ended_at')
                ->format(function ($value) {
                    return $value ? '<span class="badge badge-light-info">' . date("M jS, Y h:i A", strtotime($value)) . '</span>' : '-';
                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            Column::make('duration')
                ->format(function ($value) {
                    if (is_null($value)) {
                        return 'NA';
                    }

                    if ($value < 60) {
                        return $value . ' ' . Str::plural('Min', $value);
                    }

                    $hours = round($value / 60, 2);
                    return $hours . ' ' . Str::plural('Hr', $hours);
                }),
            Column::make('Session rate', 'session_rate'),
            Column::make('remark'),
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
            //         $builder->where('instant-consultation.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = InstantConsultationHistory::query()->with(['user' => ['country'], 'astro:id,first_name,middle_name,last_name', 'chats']);
        $from = Carbon::parse($this->start_date);
        $to = Carbon::parse($this->end_date)->endOfDay();
        if ($this->start_date && $this->end_date) {
            $modal->whereBetween('instant_consultation_histories.created_at', [$from, $to]);
        } else {
            $firstDateOfMonth = \Carbon\Carbon::now()->startOfMonth()->toDateString();
            $todayDate = \Carbon\Carbon::now()->toDateString();
            $modal->whereBetween('instant_consultation_histories.created_at', [$firstDateOfMonth, $todayDate]);
        }
        if ($this->type) {
            $modal->where('session_type', $this->type);
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
        $modelData = new InstantConsultationHistory;
        return Excel::download(new InstantConsultationExport($this->getSelected(), $modelData), 'instant-consultation.xlsx');
    }
}
