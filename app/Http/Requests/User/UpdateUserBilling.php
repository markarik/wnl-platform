<?php

namespace App\Http\Requests\User;

use App\Models\User;
use App\Models\UserBillingData;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserBilling extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		$user = User::fetch($this->route('id'));

		return
			$this->user()->can('update', $user->billing) ||
			$this->user()->id === $user->id;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'company_name' => 'required|max:250',
			'vat_id'       => 'max:250',
			'address'      => 'required|max:250',
			'zip'          => 'required|max:250',
			'city'         => 'required|max:250',
			'country'      => 'required|max:250',
		];
	}
}
