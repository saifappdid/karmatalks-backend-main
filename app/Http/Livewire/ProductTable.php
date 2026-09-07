<?php

namespace App\Http\Livewire;

use App\Exports\CustomExport;
use App\Models\Product;
use App\Services\PHPMailerService;
use Excel;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class ProductTable extends DataTableComponent
{

    protected $model = Product::class;
    public $counter  = 1;
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
                'id' => 'product-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin.products.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function ($row, Column $column) {
                    $delete_route   = route('admin.products.destroy', $row->id);
                    $redirect_route = route('admin.products.edit', $row->id);
                    $edit_callback  = 'setValue';
                    $modal          = '#edit-product-modal';
                    return view('content.table-component.action', compact('redirect_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('SrNo.', 'id')
                ->format(function ($value, $row, Column $column) {
                    return (($this->page - 1) * $this->getPerPage()) + ($this->counter++);
                })
                ->html(),
            Column::make('status')
                ->format(function ($value, $data, Column $column) {
                    $route = route('admin.products.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('Image', 'image')
                ->format(function ($value) {
                    return view('content.table-component.avatar', ['image' => $value]);
                })
                ->sortable()
                ->searchable(),
            Column::make('Type', 'type')
                ->format(function ($value, $row, Column $column) {
                    if ($value == 'product') {
                        return '<span class="badge badge-light-primary">Product</span>';
                    } elseif ($value == 'service') {
                        return '<span class="badge badge-light-info">Service</span>';
                    } else {
                        return '<span class="badge badge-light-secondary">N/A</span>';
                    }
                })->html()
                ->sortable()
                ->searchable(),
            Column::make('Category', 'category.title')
                ->format(function ($value, $row, Column $column) {
                    if ($value) {
                        return '<span class="badge badge-light-primary">' . $value . '</span>';
                    } else {
                        return '<span class="badge badge-light-secondary">N/A</span>';
                    }
                })->html()
                ->sortable()
                ->searchable(),
            Column::make('Title', 'title')
                ->sortable()
                ->searchable(),
            Column::make('price')
                ->format(function ($value, $row, Column $column) {
                    return $row->type == 'service' ? $value : '-';
                }),
            Column::make('price_dollar')
                ->format(function ($value, $row, Column $column) {
                    return $row->type == 'service' ? $value : '-';
                }),
            Column::make('Variation')
                ->label(function ($row, Column $column) {
                    if ($row->type === 'product' && count($row->variants) > 0) {
                        $variations = '';
                        foreach ($row->variants as $key => $variation) {
                            //  $variations.='<span class="badge badge-light-primary"> '.$variation->title.'</span><br>';
                            $variations .= '<span class="badge badge-light-primary"> ' . $variation->title . ' - mrp: ₹ ' . $variation->mrp_price . ' / $ ' . $variation->mrp_price_dollar . '
                                                                                                               - price: ₹ ' . $variation->price . ' / $ ' . $variation->price_dollar . '</span><br>';
                            // if ($key % 1) {
                            //     $variations .= '<br />';
                            // }
                        }
                        return $variations;
                    } else {
                        return '-';
                    }
                })
                ->html(),
            Column::make('description')
                ->format(function ($value, $row, Column $column) {
                    $html = '<span data-hidden class="d-none">' . $value . '</span>';
                    return $html .= '<button data-description class="btn btn-primary btn-sm">View</button>';
                })
                ->html(),

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
                    ''        => 'All',
                    'active'  => 'Active',
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
            //         $builder->where('products.name', 'like', '%' . $value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        $modal = Product::query()->with(['variants']);
        return $modal;
    }

    public function refresh(): void {}
    public function status($type)
    {
        $this->setFilter('status', $type);
    }

    public function exportSelected(PHPMailerService $mailer)
    {
        $modelData = new Product;
        $fileName  = 'product-services.xlsx';
        $path      = storage_path('app/' . $fileName);

        Excel::store(new CustomExport($this->getSelected(), $modelData), $fileName);

        // Use PHPMailer service to send the mail
        // $mailer->sendMail(
        //     to: 'nikhil.appdid@gmail.com',
        //     subject: 'Selected Product/Services Export',
        //     body: 'Please find attached the selected file.',
        //     attachmentPath: $path
        // );

        // $this->emit('mailSent');
        return Excel::download(new CustomExport($this->getSelected(), $modelData), 'products.xlsx');
    }
}
