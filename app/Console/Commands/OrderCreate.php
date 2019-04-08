<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class OrderCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:create {userId}';

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
        /** User info */
        $user = User::find($this->argument('userId'));

        if (!$user) {
            $this->error('No such user');
            die;
        }

        /** Product */
        $availableProducts = Product::whereNotNull('slug')
            ->get(['id', 'slug', 'name', 'price'])
            ->toArray();

        $headers = ['ID', 'slug', 'name', 'price'];
        $this->table($headers, $availableProducts);

        $productId = $this->ask('Choose product ID');

        $order = $user->orders()->create([
            'product_id' => $productId,
        ]);

        /** Payment */
        $headers = ['payment methods'];
        $paymentMethods = PaymentMethod::all(['slug']);
        $this->table($headers, $paymentMethods->toArray());

        $chosenMethod = $this->anticipate('Payment method', $paymentMethods->pluck('slug')->toArray());

        $order->fresh()->update([
            'method' => $chosenMethod
        ]);

        return;
    }
}
