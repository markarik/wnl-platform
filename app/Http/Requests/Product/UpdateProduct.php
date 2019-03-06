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
		    'name'          => 'string|max:255',
		    'invoice_name'  => 'string|max:255',
		    'slug'          => [
		    	'string',
			    'nullable',
			    'max:255',
			    Rule::unique('products', 'slug')->ignore($this->request->get('id'), 'id')
		    ],
		    'price'         => 'numeric',
		    'quantity'      => 'integer',
		    'initial'       => 'integer',
		    'delivery_date' => 'integer',
		    'course_start'  => 'integer|nullable',
		    'course_end'    => 'integer|nullable',
		    'access_start'  => 'integer|nullable',
		    'access_end'    => 'integer|nullable',
		    'signups_start' => 'integer',
		    'signups_end'   => 'integer',
		    'signups_close' => 'integer',
		    'vat_rate'      => 'numeric',
		    'vat_note'      => 'string|max:255',
	    ];
    }
}
