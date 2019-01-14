<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\InternationalTelephoneCode;
use Illuminate\Foundation\Http\FormRequest;

class CreateTTC extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'code' => ['required', new InternationalTelephoneCode],
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['required', 'string', 'size:1'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
