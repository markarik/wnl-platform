<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;

class SlideshowImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slideshow:import {lessonId} {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import slideshow from file and attach to lesson';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
	    // TODO: Generate sensible filename or take if from argument.
        $data = json_decode(Storage::get('exports/slideshow_export.json'));

        dd($data->images);

        return 0;
    }
}
