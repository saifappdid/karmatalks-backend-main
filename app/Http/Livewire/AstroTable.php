<?php

namespace App\Http\Livewire;

use App\Models\Astro;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use App\Services\PHPMailerService;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class AstroTable extends DataTableComponent
{

    protected $model = Astro::class;
    public $counter = 1;
    public function mount()
    {
        $this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        $this->counter = 1;
        $this->setFilterPillsStatus(false);

        // $this->setFiltersDisabled();
        // $this->setBulkActionsDisabled();
        $this->setColumnSelectDisabled();

        $this->setPrimaryKey('id')

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'astro-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.astros.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.astros.destroy', $row->id);
                    $redirect_route = route('admin.astros.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-astro-modal';
                    return view('content.table-component.action', compact('redirect_route'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.astros.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('profile')
                ->format(function ($value, $row, Column $column) {
                    return view('content.table-component.avatar', ['image' => $value]);
                })
                ->html(),
            Column::make('last_name')->searchable()->hideIf(true),
            Column::make('middle_name')->searchable()->hideIf(true),
            Column::make('Name', 'first_name')
                ->format(function ($value, $row, Column $column) {
                    return '<a href="' . route('admin.astros.details', $row->id) . '">' . $value . ' ' .  $row->middle_name . ' ' . $row->last_name . '</a>';
                })
                ->html()
                ->searchable(),

            Column::make('Manage Availability')
                ->label(function ($row, Column $column): string {
                    return '<a class="btn btn-primary btn-sm manageTimeLine" data-id="' . $row->id . '"><i class="fa fa-clock"></i></a>';
                })->html(),
            Column::make('Manage Pricing')
                ->label(function ($row, Column $column) {
                    return '<a class="btn btn-primary btn-sm managePrice" data-id="' . $row->id . '"><i class="fa fa-dollar"></i></a>';
                })->html(),
            Column::make('Manage Instant Pricing')
                ->label(function ($row, Column $column) {
                    return '<a class="btn btn-primary btn-sm manageInstantPrice" data-voice="' . ($row->instant_voice_call_charge) . '" data-video="' . ($row->instant_video_call_charge) . '" data-chat="' . ($row->instant_chat_charge) . '" data-id="' . $row->id . '"><i class="fa fa-dollar"></i></a>';
                })->html(),
            Column::make('Instant Voice Call Charge', 'instant_voice_call_charge')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                })
                ->searchable(),
            Column::make('Instant Video Call Charge', 'instant_video_call_charge')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                }),
            Column::make('Instant Chat', 'instant_chat_charge')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                }),
            Column::make('Instant Voice Call Charge in Dollar', 'instant_voice_call_charge_dollar')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                })
                ->searchable(),
            Column::make('Instant Video Call Charge in Dollar', 'instant_video_call_charge_dollar')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                }),
            Column::make('Instant Chat in Dollar', 'instant_chat_charge_dollar')
                ->format(function ($value) {
                    return !empty($value) ?  $value : '-';
                }),
            Column::make('Email', 'email')->searchable(),
            Column::make('Mobile', 'mobile')->searchable(),
            Column::make('Gender', 'gender')
                ->format(function ($value) {
                    return '<span class="badge badge-light-info">' . ucfirst($value) . '</span>';
                })
                ->html()
                ->searchable(),
            Column::make('Experience', 'experience')->searchable(),
            Column::make('Languages', 'known_languages')
                ->format(function ($value) {
                    $languages = json_decode($value);
                    $output = '';
                    if (!empty($languages)) {
                        foreach ($languages as $language) {
                            $output .= '<span class="badge badge-light-primary mr-1 mb-1">' . $language->name . '</span>';
                        }
                    }
                    return $output;
                })
                ->html()
                ->searchable(),
            Column::make('Skills', 'skills')
                ->label(function ($row, Column $column) {
                    $skills = $row->skills;
                    $output = '';
                    if (!empty($skills)) {
                        foreach ($skills as $skill) {
                            $output .= '<span class="badge badge-light-success mr-1 mb-1">' . $skill->name . '</span>';
                        }
                    }
                    return $output;
                })
                ->html()
                ->searchable(),
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

            DateFilter::make('From Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('created_at', '>=', $value);
                }),

            DateFilter::make('To Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('created_at', '<=', $value);
                }),

            // TextFilter::make('Name')
            //     ->config([
            //         'placeholder' => 'Search Name',
            //         'maxlength' => '25',
            //     ])
            //     ->filter(function (Builder $builder, string $value) {
            //         $builder->where('astros.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Astro::query();
        $modal->with(['skills']);
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    // public function exportSelected()
    // {
    //     $modelData = new Astro;
    //     return Excel::download(new CustomExport($this->getSelected(), $modelData), 'astros.xlsx');
    // }
    public function exportSelected(PHPMailerService $mailer)
    {
        $modelData = new Astro;

        $fileName = 'astros.xlsx';
        $path = storage_path('app/' . $fileName);

        Excel::store(new CustomExport($this->getSelected(), $modelData), $fileName);

        // Use PHPMailer service to send the mail
        // $mailer->sendMail(
        //     to: 'nikhil.appdid@gmail.com',
        //     subject: 'Selected Users Export',
        //     body: 'Please find attached the selected users file.',
        //     attachmentPath: $path
        // );

        // $this->emit('mailSent');
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'sources.xlsx');
    }
}
