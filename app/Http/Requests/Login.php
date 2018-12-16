<?php

namespace App\Http\Requests;

use App\Rules\OnlyNumber;
use App\Rules\InternationalTelephoneCode;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\VerifyPhoneTextVerificationCode;

class Login extends FormRequest
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
            'verify_type' => [
                'required', 'string', 'in:phone,password',
            ],
            'international_telephone_code' => [
                'required', 'string',
                new InternationalTelephoneCode,
            ],
            'phone' => [
                'required', 'string', new OnlyNumber,
            ],
            'verification_code' => [
                'required_if:verify_type,phone', 'numeric',
                new VerifyPhoneTextVerificationCode($this, 'phone'),
            ],
            'password' => [
                'required_if:verify_type,password', 'string',
            ],
        ];
    }
}
