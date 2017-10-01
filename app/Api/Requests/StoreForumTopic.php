<?php

namespace App\Api\Requests;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class StoreForumTopic extends FormRequest
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
     * Get the rules.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'subject' => 'required|string|min:6|max:255',
            'body' => 'required|string|min:1|max:3000',
        ];
    }

    /**
     * Validate caregory.
     *
     * @return void
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function validateCategory(Closure $withValidate)
    {
        $factory = $this->container->make(ValidationFactory::class);
        $validator = $factory->make(
            $this->validationData(), ['category' => 'required|numeric'],
            $this->messages(), $this->attributes()
        );

        if (! $validator->passes()) {
            $this->failedValidation($validator);
        }

        $withValidate($validator, function () use ($validator) {
            $this->failedValidation($validator);
        });
    }
}
