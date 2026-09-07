<?php

namespace App\Console\Commands;

use App\Models\Astro;
use App\Models\AstroSkill;
use Illuminate\Console\Command;

class UpdateAstroSkills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:astro-skills';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update astro skills';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $astros = Astro::whereNotNull('skills')->get();

        foreach ($astros as $astro) {
            $skills = json_decode($astro->skills, true);
            foreach ($skills as $key => $skillId) {
                AstroSkill::updateOrCreate(
                    ['astro_id' => $astro->id, 'skill_id' => $skillId['id']]
                );
            }
        }

        return $this->info('Astro skills updated successfully.');
    }
}
