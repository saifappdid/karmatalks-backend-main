<?php

namespace App\Http\Livewire;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use App\Services\PHPMailerService;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;


class ServiceTable extends DataTableComponent
{

    protected $model = Service::class;
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
        // $this->setBulkActionsDisabled();
        $this->setColumnSelectDisabled();

        $this->setPrimaryKey('id')

            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => 'service-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive',
                //     [
                //         'route' => 'admin.services.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route = route('admin.services.destroy', $row->id);
                    $edit_route = route('admin.services.edit', $row->id);
                    $edit_callback = 'setValue';
                    $modal = '#edit-service-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.services.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('image')->format(function ($value) {
                return view('content.table-component.avatar', ['image' => $value]);
            })
                ->sortable()
                ->searchable(),
            Column::make('title')
                ->sortable()
                ->searchable(),
            Column::make('description')
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
            //         $builder->where('services.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Service::query();
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected(PHPMailerService $mailer)
    {
        $modelData = new Service;
        $fileName = 'services.xlsx';
        $path = storage_path('app/' . $fileName);

        Excel::store(new CustomExport($this->getSelected(), $modelData), $fileName);

        // Use PHPMailer service to send the mail
        $mailer->sendMail(
            to: 'nikhil.appdid@gmail.com',
            subject: 'Selected Services Export',
            body: 'Please find attached the selected services file.',
            attachmentPath: $path
        );

        $this->emit('mailSent');
        // return Excel::download(new CustomExport($this->getSelected(), $modelData), 'services.xlsx');
    }
}
