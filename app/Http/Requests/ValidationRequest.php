<?php

namespace App\Http\Requests;

use App\Service\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidationRequest extends FormRequest
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
            'amount'         => 'required|numeric',
            'date'           => 'required',
            'image'          => 'nullable|image|mimes:jpeg,jpg,png,gif,JPG,JPEG|max:1024',
        ];
    }

    /**
     * Return Custom Attribute For Custom Message
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'no_transaction' => 'No Transaction',
            'user_id'        => 'User',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error_validation($validator)
        );
    }
}
