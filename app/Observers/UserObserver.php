<?php

namespace App\Observers;


use App\Mail\SignUpConfirmation;
use App\Models\Order;
use App\Jobs\OrderPaid;
use App\Jobs\OrderConfirmed;
use App\Models\User;
use Illuminate\Support\Facades\App;
use App\Notifications\OrderCreated;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Mail;


class UserObserver
{
	use DispatchesJobs, Notifiable;

	public function created(User $user)
	{
		Mail::to($user)->send(new SignUpConfirmation($user));

		// Populate user tables.
		// This duplication of user data is needed just to keep payment working at the moment,
		// because we're migrating from single user data table to multiple user tables.
		// It can be removed as soon, as new/refactored payment is ready.
		$user->profile()->firstOrCreate([
			'first_name' => $user->first_name,
			'last_name'  => $user->last_name,
		]);

		$user->billing()->firstOrCreate([
			'company_name' => $user->invoice_name,
			'vat_id'       => $user->invoice_nip,
			'address'      => $user->invoice_address,
			'zip'          => $user->invoice_zip,
			'city'         => $user->invoice_city,
			'country'      => $user->invoice_country,
		]);
	}
}
