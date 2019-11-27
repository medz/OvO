<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListComments extends FormRequest
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
            'id.*' => ['required_with:id', 'integer'],
            'sort' => ['nullable', 'string', Rule::in('id')],
            'direction' => ['nullable', 'string', Rule::in('asc', 'desc')],
            'publisher' => ['nullable', 'integer', 'min:1'],
            'commentable_type' => ['required_with:commentable_id', 'string'],
            'commentable_id' => ['required_with:commentable_type', 'integer', 'min:1'],
        ];
    }
}
