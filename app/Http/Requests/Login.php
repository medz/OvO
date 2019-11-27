<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\HasEnabledITC;
use App\Rules\InternationalTelephoneCode;
use App\Rules\OnlyNumber;
use App\Rules\VerifyPhoneTextVerificationCode;
use Illuminate\Foundation\Http\FormRequest;

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
            'international_telephone_code' => [
                'required', 'string',
                new InternationalTelephoneCode,
                new HasEnabledITC,
            ],
            'phone' => [
                'required', 'string', new OnlyNumber,
            ],
            'verification_code' => [
                'required', 'numeric',
                new VerifyPhoneTextVerificationCode($this),
            ],
        ];
    }
}
