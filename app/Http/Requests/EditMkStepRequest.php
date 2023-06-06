<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EditMkStepRequest extends FormRequest
{
    public function rules()
    {
        return [
            'description' => 'required|string',
        ];
    }

    public function failedValidations(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validations failed',
            'data'=> $validator->errors()
        ]));
/*         return [
            'description.required' => 'Description is required',
          
        ]; */
    }

    public function messages()
    {
        return [
            'description.required' => 'Description is required'
        ];
    }
}
