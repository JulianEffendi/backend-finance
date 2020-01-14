<?php

namespace App\Http\Requests;

use App\Service\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class TransactionRequest extends FormRequest
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
        $statusRequired = 'required';
        if ($this->pending) {
            $statusRequired = 'nullable';
        }

        return [
            'name'           => 'required|string',
            'amount'         => $statusRequired.'|numeric',
            'date'           => $statusRequired,
            'type_id'        => 'required|numeric',
            'file'           => 'nullable|image|mimes:jpeg,jpg,png,gif,JPG,JPEG|max:1024',
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
            'type_id'        => 'Transaction Type'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error_validation($validator)
        );
    }
}
