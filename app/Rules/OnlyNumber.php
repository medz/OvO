<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OnlyNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return (bool) preg_match('/[0-9]{8,20}/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.only_number');
    }
}
