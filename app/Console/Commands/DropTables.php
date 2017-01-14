<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DropTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:droptables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables in current database';

    /**
     * Create a new command instance.
     *
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
        if (env('APP_ENV') !== 'dev'){
            exit('This command works only in dev environment!' . PHP_EOL);
        }

        if (!$this->confirm('CONFIRM DROP AL TABLES IN THE CURRENT DATABASE? [y|N]')) {
            exit('Drop Tables command aborted' . PHP_EOL);
        }

        $colname = 'Tables_in_' . env('DB_DATABASE');

        $tables = DB::select('SHOW TABLES');

        foreach ($tables as $table) {

            $droplist[] = $table->$colname;

        }
        $droplist = implode(',', $droplist);

        DB::beginTransaction();
        //turn off referential integrity
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::statement("DROP TABLE $droplist");
        //turn referential integrity back on
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        DB::commit();

        $this->comment(PHP_EOL . "If no errors showed up, all tables were dropped" . PHP_EOL);
    }
}
