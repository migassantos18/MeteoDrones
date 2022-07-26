<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ConfigsStoreRequest extends FormRequest
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
            'type' => 'required',
            'time' => 'numeric|nullable',
            'meters' => 'numeric|nullable'
        ];
    }

    public function messages()
    {
        return [
            'time.numeric' => 'O tempo tem de ser um valor numérico',
            'meters.numeric' => 'A distancia tem de ser um valor numérico',
        ];
    }
}
