<?php namespace App\Console\Commands;

use App\Models\Slide;
use Illuminate\Console\Command;

class SlidesSnippets extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'slides:snippets';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Regenerates all slides snippets.';

	/**
	 * Create a new command instance.
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
		$slides = Slide::all();

		$bar = $this->output->createProgressBar(count($slides));

		foreach ($slides as $slide) {
			$slide->snippet = $slide->content;
			$slide->save();
			$bar->advance();
		}

		print PHP_EOL;
	}
}
