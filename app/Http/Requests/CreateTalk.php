<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\ModelExists;
use Illuminate\Foundation\Http\FormRequest;

class CreateTalk extends FormRequest
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
            'content' => ['bail', 'required', 'string', 'max:255'],
            'searchable' => ['bail', 'nullable', 'array'],
            'shareable.type' => ['bail', 'required_with:searchable', 'string', 'in:talks,forum:threads'],
            'shareable.id' => ['bail', 'required_with:searchable', 'string', new ModelExists($this, 'shareable.type')],
            'media' => ['bail', 'nullable', 'array'],
            'media.*' => ['bail', 'required_with:media', 'string'],
        ];
    }
}
