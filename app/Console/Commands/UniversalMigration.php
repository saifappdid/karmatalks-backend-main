<?php

namespace App\Console\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UniversalMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to migrate files from local machine to S3 bucket.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->ask('Enter the model name (e.g., User, Post)');
        $column = $this->ask('Enter the column name that contains the file paths (e.g., image, profile)');
        $model = "App\\Models\\$model";
        $data = $model::whereNotNull($column)->get();
        foreach ($data as $key => $file) {
            $checkAlreadyMoved = str_contains($file->$column, 'karmatalks.blr1');
            if (!$checkAlreadyMoved) {
                try {
                    $parsedPath = parse_url($file->$column, PHP_URL_PATH);
                    $cleanPath = ltrim($parsedPath, '/');
                    $content = Storage::disk('appdidS3')->get($cleanPath);
                    $path = $cleanPath;
                    $url = Storage::disk('s3')->put($path, $content, 'public');
                    $spaceUrl = Storage::disk('s3')->url($path);
                    $file->$column = $spaceUrl;
                    $file->save();
                } catch (\Throwable $th) {
                    \Log::error($th->getMessage());
                    \Log::info('Error in  migration: ' . $file->id);
                }
            }
        }
        return $this->info('Migration completed successfully!');
    }
}
