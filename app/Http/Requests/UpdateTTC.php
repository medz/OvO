<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\InternationalTelephoneCode;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTTC extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['nullable', new InternationalTelephoneCode],
            'name' => ['nullable', 'string', 'max:100'],
            'icon' => ['nullable', 'string'],
            'enabled' => ['nullable', 'boolean'],
        ];
    }
}
