<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
	use HandlesAuthorization;

	public function before($user, $ability)
	{
		if ($user->isAdmin()) {
			return true;
		}

		return null;
	}

	public function view(User $user, Invoice $invoice)
	{
		return $user->id === $invoice->order->user->id;
	}
}
