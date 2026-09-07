<?php

namespace App\Http\Livewire;

use Excel;
use App\Models\User;
use Illuminate\Support\Str;
use App\Exports\CustomExport;
use App\Services\PHPMailerService;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\DateFilter;



class UserTable extends DataTableComponent
{

    protected $model = User::class;
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
                'id' => 'source-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.users.index',
                //     ],
                // ],
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.users.destroy', $row->id);
                    $edit_route = route('admin.users.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-user-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            // Column::make('status')
            //     ->format(function ($value, $data, Column $column) {
            //         $route = route('admin.users.status');
            //         return view('content.table-component.switch', compact('data', 'route'));
            //     }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('name')
                ->format(function ($value, $row, Column $column) {
                    // return '<a href="' . route('admin.users.profile', $row->id) . '">' . $value . '</a>';
                    return '<a href="' . route('admin.users.profile', $row->id) . '">' . $value . '</a>';
                })
                ->html()
                ->sortable()
                ->searchable(),
            Column::make('image')
                ->format(function ($value, $row, Column $column) {
                    return view('content.table-component.avatar', ['image' => $value]);
                })->html(),
            Column::make('phone')
                ->sortable()
                ->searchable(),
            Column::make('email')
                ->sortable()
                ->searchable(),
            Column::make('wallet'),
            Column::make('gender')
                ->format(function ($value, $row, Column $column) {
                    return Str::ucfirst($value);
                }),
            Column::make('dob')
                ->format(function ($value, $row, Column $column) {
                    return $value ? date("M jS, Y", strtotime($value)) : '-';
                }),
            Column::make('Country', 'country_id')
                ->format(function ($value, $row, Column $column) {
                    return isset($row->country) ? $row->country->name : '-';
                }),

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
                    'completed' => 'Completed',
                    'incompleted' => 'Incompleted',
                ])
                ->filter(function (Builder $builder, string $value) {
                    if ($value == 'completed') {
                        $builder->whereNotNull('name');
                    }
                    else {
                        $builder->whereNull('name');
                    }
                }),

            DateFilter::make('From Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('created_at', '>=', $value);
                }),

            DateFilter::make('To Date')
                ->filter(function (Builder $builder, string $value) {
                    $builder->whereDate('created_at', '<=', $value);
                }),
        ];
    }

    public function builder(): Builder
    {
        $modal = User::query();
        $modal->with(['country']);
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected(PHPMailerService $mailer)
    {
        $modelData = new User;

        $fileName = 'users.xlsx';
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
