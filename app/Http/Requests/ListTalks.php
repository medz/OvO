<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ListTalks extends FormRequest
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
            'query' => ['nullable', 'string'],
            'id' => ['nullable', 'array'],
            'id.*' => ['required_with:id', 'string'],
            'publisher' => ['nullable', 'array'],
            'publisher.*' => ['required_with:publisher', 'string'],
        ];
    }
}
