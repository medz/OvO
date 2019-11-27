<?php

declare(strict_types=1);

namespace App\Rules;

use App\VerificationCode;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Overtrue\EasySms\PhoneNumber;

class VerifyPhoneTextVerificationCode implements Rule
{
    /**
     * The validate phone number.
     */
    protected $phoneNumber;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request, string $phoneNumberField = 'phone', string $TTC = 'international_telephone_code')
    {
        $this->phoneNumber = new PhoneNumber($request->{$phoneNumberField}, $request->{$TTC});
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return VerificationCode::instance($this->phoneNumber)->has(false, (string) $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.text-code-validate-error');
    }
}
