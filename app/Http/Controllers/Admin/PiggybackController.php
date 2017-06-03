<?php namespace App\Http\Controllers\Admin;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PiggybackController extends Controller
{
	public function index($userId)
	{
		$targetUser = User::find($userId);

		if (!$targetUser) abort(404);

		Auth::login($targetUser);

		return redirect(route('home'));
	}
}
