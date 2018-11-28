<?php

namespace App\Http\Requests;

use App\Models\SiteWideMessage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteWideMessage extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'slug' => 'string|nullable',
			'message' => 'string|nullable',
			'start_date' => 'integer|nullable',
			'end_date' => 'integer|nullable',
		];
	}

	protected function getValidatorInstance()
	{
		$data = $this->all();
		if ($data['start_date'] === '') {
			$data['start_date'] = null;
		}
		if ($data['end_date'] === '') {
			$data['end_date'] = null;
		}

		$this->getInputSource()->replace($data);

		/*modify data before send to validator*/

		return parent::getValidatorInstance();
	}
}
