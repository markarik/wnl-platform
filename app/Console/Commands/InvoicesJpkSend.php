<?php

namespace App\Console\Commands;

use App\Mail\JpkReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class InvoicesJpkSend extends CommandWithMonitoring
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
    protected $description = 'Send JPK file to accountant';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handleBody()
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
