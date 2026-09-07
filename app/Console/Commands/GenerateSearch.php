<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will generate the search index for the dashboard';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $menu = resource_path('data/menu-data/verticalMenu.json');
        $menu = json_decode(file_get_contents($menu), true);
        $search_menu  = [];
        foreach ($menu['menu'] as $key => $m) {
            if (!empty($m['navheader'])) {
                continue;
            }
            if (!empty($m['submenu'])) {
                foreach ($m['submenu'] as $key => $sm) {
                    $search_menu['listItems'][] = $sm;
                }
            } else {
                $search_menu['listItems'][] = $m;
            }
        }

        File::put(resource_path('data/search.json'), json_encode($search_menu));
        File::put(public_path('data/search.json'), json_encode($search_menu));

        $this->info('Search index generated');
    }
}
