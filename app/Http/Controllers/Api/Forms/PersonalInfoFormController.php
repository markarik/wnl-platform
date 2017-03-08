<?php
namespace App\Http\Controllers\Api\Forms;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Kris\LaravelFormBuilder\FormBuilder;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Http\Forms\PersonalInfoForm;
use Illuminate\Support\Facades\Auth;

class PersonalInfoFormController extends Controller {
	use FormBuilderTrait;

	function getForm($userId = 0) {
		$form = $this->form(PersonalInfoForm::class, [
			'model' => Auth::user(),
		]);
		dd($form);
		// return response()->json($form);
	}

	function getSchemeAndData() {

	}

	function saveData() {

	}
}
