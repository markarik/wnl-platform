<?php

namespace App\Console\Commands;

use App\Mail\JpkReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class InvoicesJpkSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:jpk-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $dateFrom = Carbon::now()->subMonth()->startOfMonth();
        $dateTo = Carbon::now()->subMonth()->endOfMonth();

        $filename = sprintf('jpk-%s-%s.xml',
            $dateFrom->format('Ymd'),
            $dateTo->format('Ymd')
        );

        $this->call('invoices:jpk', [
            'from' => $dateFrom->format('Y-m-d'),
            'to' => $dateTo->format('Y-m-d'),
            '--filename' => $filename,
        ]);

        $users = User::ofRole('accountant');

        if ($users->count() < 1) {
            $this->error('JPK file has not been sent. There must be at least one user having \'accountant\' role.');
            return;
        }

        Mail::send(new JpkReport($filename, $users));

        return;
    }
}
