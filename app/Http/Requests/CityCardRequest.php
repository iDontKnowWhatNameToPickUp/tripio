<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityCardRequest extends FormRequest
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
            'city_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048'
        ];
    }
}
