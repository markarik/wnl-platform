<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TermsOfUseController extends Controller
{
	public function index()
	{
		$user = \Auth::user();
		if ($user->consent_terms) {
			return redirect('/app');
		}

		return view('auth.terms-change', ['user' => $user]);
    }

	public function accept()
	{
		$user = \Auth::user();
		$user->consent_terms = true;
		$user->save();

		return redirect('/app');
    }
}
