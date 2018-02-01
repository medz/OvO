<?php

namespace App\Api\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResgisterUser extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get rules.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'login' => 'required|string|unique:users,login',
            'password' => 'required|confirmed',
        ];
    }
}
