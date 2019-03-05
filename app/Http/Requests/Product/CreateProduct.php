<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class CreateProduct extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return Auth::user()->isAdmin();
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name'          => 'required|string|max:255',
			'invoice_name'  => 'required|string|max:255',
			'slug'          => 'required|string|max:255|unique:products,slug',
			'price'         => 'required|numeric',
			'quantity'      => 'required|integer',
			'initial'       => 'required|integer',
			'delivery_date' => 'required|integer',
			'course_start'  => 'required|integer',
			'course_end'    => 'required|integer',
			'access_start'  => 'required|integer',
			'access_end'    => 'required|integer',
			'signups_start' => 'required|integer',
			'signups_end'   => 'required|integer',
			'signups_close' => 'required|integer',
			'vat_rate'      => 'required|numeric',
			'vat_note'      => 'required|string|max:255',
		];
	}
}
