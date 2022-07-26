<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class DataStoreRequest extends FormRequest
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
            'temperature' => 'required|numeric',
            'humidity' => 'required|numeric',
            'heat_index' => 'required|numeric',
            'latitude' => 'present|numeric|nullable',
            'longitude' => 'present|numeric|nullable',
            'altitude' => 'present||numeric|nullable',
            'date' => 'required|string',
            'time' => 'required|string',
            'luminosity' => 'present|numeric|nullable'
        ];
    }

    public function messages()
    {
        return [
            'temperature.required' => 'Temperature is required',
            'humidity.required' => 'Humidity is required',
            'heat_index.required' => 'Heat index is required',
            'temperature.numeric' => 'A temperature must be a number',
            'humidity.numeric' => 'A humidity must be a number',
            'heat_index.numeric' => 'A heat_index must be a number',
            'latitude.present' => 'Latitude is required',
            'longitude.present' => 'Longitude is required',
            'altitude.present' => 'Altitude is required',
            'date.required' => 'Date is required',
            'time.required' => 'Time is required',
            'latitude.numeric' => 'Latitude must be a number',
            'longitude.numeric' => 'Longitude must be a number',
            'altitude.numeric' => 'Altitude must be a number',
            'date.string' => 'Date must be a string',
            'time.numeric' => 'Time must be a string',
            'luminosity.present' => 'Luminosity is required',
            'luminosity.numeric' => 'Luminosity must be a number'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => $validator->errors()
        ], 400));
    }
}
