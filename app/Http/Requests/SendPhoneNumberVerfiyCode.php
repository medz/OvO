<?php

namespace App\Http\Requests;

use App\Rules\OnlyNumber;
use App\Rules\InternationalTelephoneCode;
use Illuminate\Foundation\Http\FormRequest;

class SendPhoneNumberVerfiyCode extends FormRequest
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
            'phone' => [
                'required', 'string', new OnlyNumber
            ],
            'international_telephone_code' => [
                'required', 'string',
                new InternationalTelephoneCode,
            ],
        ];
    }
}
