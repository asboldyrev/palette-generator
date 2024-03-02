<?php

namespace App\Console\Commands;

use App\Services\YaColors\Models\Image;
use Illuminate\Console\Command;

class RegeneratePalette extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:regenerate-palette';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $images = Image::all();

        /** @var Image $image */
        foreach ($images as $image) {
            $image->update();
            $this->info($image->fileInfo->id);
        }
    }
}
