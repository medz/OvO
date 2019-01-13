<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Jurisdiction;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class SyncUserJurisdictionNodes extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nodes' => 'required|array',
            'nodes.*.node' => ['required', Rule::in(Jurisdiction::nodes())],
        ];
    }
}
