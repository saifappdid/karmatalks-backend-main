<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:all {name} {--columns=* : Enter Columns} {--col=} {--namespace=} {--blade} {--controller} {--model} {--blade} {--datatable} {--all} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ask = $this->ask("Want to define your Columns ? (y/n)");
        $columnsArray = null;
        if ($ask == "y") {
            $columns = $this->ask("Write Your Column Names with comma separated and without space");
            $columnsArray = explode(',', $columns);
        }

        $this->info('Under Process Please Be Patient.....');
        $model = $this->argument('name');
        $name = strtolower($this->argument('name'));
        $cols = explode(',', $this->option('col'));
        $namespace = (!empty($this->option('namespace'))) ? 'namespace App\Http\Controllers\Admin\\' . $this->option('namespace') . ';' : 'namespace App\Http\Controllers\Admin;';

        if (!empty($this->option('model')) || $this->option('all')) {
            \Artisan::call('make:model ' . $model . ' -m');
        }

        $datatableFile = "<?php
namespace App\Http\Livewire;

use App\Models\\$model;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Excel;
use App\Exports\CustomExport;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

        
class " . $model . "Table extends DataTableComponent
{

    protected \$model = " . $model . "::class;
    public \$counter = 1;
    public function mount()
    {
        \$this->dispatchBrowserEvent('table-refreshed');
    }

    public function configure(): void
    {
        \$this->counter = 1;
        \$this->setFilterPillsStatus(false);

        \$this->setFiltersDisabled();
        \$this->setBulkActionsDisabled();
        \$this->setColumnSelectDisabled();

        \$this->setPrimaryKey('id')
        
            ->setDefaultSort('id', 'desc')
            ->setEmptyMessage('No Result Found')
            ->setTableAttributes([
                'id' => '" . $name . "-table',
            ])
            ->setBulkActions([
                'exportSelected' => 'Export',
            ])
            ->setConfigurableAreas([
                'toolbar-right-end' => 'content.rapasoft.add-button',
                // 'toolbar-left-end' => [
                //     'content.rapasoft.active-inactive', [
                //         'route' => 'admin." . $name . "s.index',
                //     ]
                // ]
            ]);
    }

    public function columns(): array
    {
        return [
            Column::make('Actions')
                ->label(function (\$row, Column \$column) {
                    \$delete_route = route('admin." . $name . "s.destroy', \$row->id);
                    \$edit_route = route('admin." . $name . "s.edit', \$row->id);
                    \$edit_callback = 'setValue';
                    \$modal = '#edit-$name-modal';
                    return view('content.table-component.action', compact('edit_route', 'delete_route', 'edit_callback', 'modal'));
                }),
            Column::make('status')
                ->format(function (\$value, \$data, Column \$column) {
                    \$route = route('admin." . $name . "s.status');
                    return view('content.table-component.switch', compact('data', 'route'));
                }),
            Column::make('SrNo.', 'id')
            ->format(function (\$value, \$row, Column \$column) {
                return ((\$this->page - 1) * \$this->getPerPage()) + (\$this->counter++);
            })
            ->html(),
                ";

        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $datatableFile .= "Column::make('$value')
                                            ->sortable()
                                            ->searchable(), ";
            }
        }


        $datatableFile .= " 
                
                // Column::make('image')
                // ->format(function (\$row) {
                //     if (\$row) {
                //         return '<img src=\"' . asset(\$row) . '\" class=\"view-on-click  rounded-circle\">';
                //     } else {
                //         return '<img src=\"' . asset('images/placeholder.jpg') . '\" class=\"view-on-click  rounded-circle\">';
                //     }
                // })
                // ->html(),

                Column::make('Created at', 'created_at')
                ->format(function (\$value) {
                    return '<span class=\"badge badge-light-success\">' . date(\"M jS, Y h:i A\", strtotime(\$value)) . '</span>';

                })
                ->html()
                ->collapseOnTablet()
                ->sortable(),
            // Column::make('Updated at', 'updated_at')
            //     ->format(function (\$value) {
            //        return '<span class=\"badge badge-light-success\">' . date(\"M jS, Y h:i A\", strtotime(\$value)) . '</span>';

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
            ->filter(function (Builder \$builder, string \$value) {
                \$builder->where('status', \$value);
            }),

            // TextFilter::make('Name')
            //     ->config([
            //         'placeholder' => 'Search Name',
            //         'maxlength' => '25',
            //     ])
            //     ->filter(function (Builder \$builder, string \$value) {
            //         \$builder->where('" . $name . "s.name', 'like', '%' . \$value . '%');
            //     }),
        ];
    }

    public function builder(): Builder
    {
        \$modal = $model::query();
        return \$modal;
    }

    public function refresh(): void
    {

    }
    public function status(\$type)
    {
        \$this->setFilter('status', \$type);
    }

    public function exportSelected()
    {
        \$modelData = new $model;
        return Excel::download(new CustomExport(\$this->getSelected(), \$modelData), '" . $name . "s.xlsx');
    }
}
        ";




        $controller = "<?php
        $namespace
        use Hash;
        use App\Models\\" . $model . ";
        use Illuminate\Http\Request;
        use App\Http\Controllers\Controller;
        use Excel;
        use App\Exports\GeneralExport;


        class " . $model . "Controller extends Controller
        {
        public function index()
        {
        return view('content.tables." . $name . "s');
        }
        public function store(Request \$request)
        {
            \$data = new $model;
            ";

        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $controller .= "\$data->$value = \$request->$value;";
            }
        }

        $controller .= "
            \$data->save();
            return response([
                'header' => 'Success!',
                'message' => '" . $name . " added successfully',
                'table' => '" . $name . "-table',
            ]);
        }
        public function edit(\$id)
        {
        \$data = " . $model . "::findOrFail(\$id);
        return response(\$data);
        }

        public function update(Request \$request)
        {
            \$data = " . $model . "::findOrFail(\$request->id);";

        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $controller .= "\$data->$value = \$request->$value;";
            }
        }

        $controller .= "
            \$data->save();
            return response([
                'header' => 'Success!',
                'message' => '" . $name . " updated successfully',
                'table' => '" . $name . "-table',
            ]);
        }

        public function status(Request \$request)
        {
        \$request->validate([
            'id' => 'required|numeric|exists:" . $name . "s,id',
            'status' => 'required|in:active,blocked',
        ]);

        " . $model . "::findOrFail(\$request->id)->update(['status' => \$request->status]);

        return response([
            'message' => '" . $name . " status updated successfully',
            'table' => '" . $name . "-table',
        ]);
        }

        public function destroy(\$id)
        {
            " . $model . "::findOrFail(\$id)->delete();
            return response([
                'header' => 'Success!',
                'message' => '" . $name . " deleted successfully',
                'table' => '" . $name . "-table',
            ]);
        }
        }
        ";

        $blade = '@extends(\'layouts/contentLayoutMaster\')

        @section(\'title\', "' . $model . '")
        @section(\'page-style\')
        <style>
            [x-cloak] { display: none !important; }
            .dropdown-menu {
            transform: scale(1)!important;
                }
        </style>
        @endsection

        @section(\'content\')

        <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                <livewire:' . $name . '-table />
                </x-card>
            </div>
        </div>
        </section>


        <x-side-modal title="Add ' . $name . '" id="add-blade-modal">
        <x-form id="add-' . $name . '" method="POST" class="" :route="route(\'admin.' . $name . 's.store\')">
            <div class="col-md-12 col-12 ">';

        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $blade .= '<x-input :required="false" name="' . $value . '" />';
            }
        } else {
            $blade .= '<x-input :required="false" name="name" />';
        }

        $blade .= '
            </div>
           </x-form>
        </x-side-modal>
        <x-side-modal title="Update ' . $name . '" id="edit-' . $name . '-modal">
        <x-form id="edit-' . $name . '" method="POST" class="" :route="route(\'admin.' . $name . 's.update\')">

            <div class="col-md-12 col-12 ">';


        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $blade .= '<x-input :required="false" name="' . $value . '" />';
            }
        } else {
            $blade .= '<x-input :required="false" name="name" />';
        }

        $blade .= '
                <x-input name="id" type="hidden" />
            </div>

        </x-form>
        </x-side-modal>
        @endsection
        @section(\'page-script\')
        <script>
        $(document).ready(function() {
            $(document).on(\'click\', \'[data-show]\', function() {
                const modal = $(this).data(\'show\');
                $(`#${modal}`).modal(\'show\');
            });
        });

        function setValue(data, modal) {
            $(`${modal} #id`).val(data.id);';

        if (isset($columnsArray)) {
            foreach ($columnsArray as $value) {
                $blade .= '$(`${modal} #' . $value . '`).val(data.' . $value . ');';
            }
        } else {
            $blade .= '$(`${modal} #name`).val(data.name);';
        }
        $blade .= '
            
            $(modal).modal(\'show\');
        }
        </script>
        @endsection';

        //datatable store
        if (!empty($this->option('datatable')) || $this->option('all')) {
            $datatable_path = "app/Http/Livewire/" . $model . "Table.php";
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $datatable_path, "wb");
            fwrite($fp, $datatableFile);
            fclose($fp);
        }
        //controller store
        if (!empty($this->option('controller')) || $this->option('all')) {
            if (!empty($this->option('namespace'))) {
                $controller_path = "app/Http/Controllers/Admin/" . $this->option('namespace') . "/" . $model . "Controller.php";
            } else {
                $controller_path = "app/Http/Controllers/Admin/" . $model . "Controller.php";
            }
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $controller_path, "wb");
            fwrite($fp, $controller);
            fclose($fp);
        }
        //blade store
        if (!empty($this->option('blade')) || $this->option('all')) {
            $datatable_path = "resources/views/content/tables/" . $name . "s.blade.php";
            $fp = fopen($_SERVER['DOCUMENT_ROOT'] . $datatable_path, "wb");
            fwrite($fp, $blade);
            fclose($fp);
        }
        $this->info('Table created successfully.');
    }
}
