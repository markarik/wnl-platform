<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Validation\Rule;

class UpdateProduct extends FormRequest
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
		    'name'          => 'string|max:255|required',
		    'invoice_name'  => 'string|max:255|required',
		    'slug'          => [
		    	'string',
			    'nullable',
			    'max:255',
			    Rule::unique('products', 'slug')->ignore($this->request->get('id'), 'id')
		    ],
		    'price'         => 'numeric|required',
		    'quantity'      => 'integer|required',
		    'initial'       => 'integer|required',
		    'delivery_date' => 'integer|required',
		    'course_start'  => 'integer|nullable',
		    'course_end'    => 'integer|nullable',
		    'access_start'  => 'integer|nullable',
		    'access_end'    => 'integer|nullable',
		    'signups_start' => 'integer|required',
		    'signups_end'   => 'integer|required',
		    'signups_close' => 'integer|required',
		    'vat_rate'      => 'numeric|required',
		    'vat_note'      => 'string|max:255|nullable',
	    ];
    }
}
