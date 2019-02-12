<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateForumNode extends FormRequest
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
            'name' => ['required', 'string', 'max:50', 'unique:forum_nodes,name'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
        ];
    }
}
