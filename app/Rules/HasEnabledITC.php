<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\InternationalTelephoneCode;
use Illuminate\Contracts\Validation\Rule;

class HasEnabledITC implements Rule
{
    protected $value;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->value = $value;

        return boolval(
            InternationalTelephoneCode::where('code', $value)->first()->enabled_at ?? false
        );
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.international_telephone_code.disabled', [
            'code' => $this->value,
        ]);
    }
}
