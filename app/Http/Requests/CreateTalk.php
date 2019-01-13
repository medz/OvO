<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Talk;
use App\ModelMorphMap;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateTalk extends FormRequest
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
        return array_merge([
            'content' => ['bail', 'required', 'string', 'max:255'],
            'repostable' => ['bail', 'nullable', 'array'],
            'repostable.type' => ['bail', 'required_with:repostable', 'string', Rule::in(ModelMorphMap::classAliases())],
            'repostable.id' => ['bail', 'required_with:repostable', 'integer', 'min:0'],
            'resource_type' => ['bail', 'nullable', 'string', Rule::in(Talk::RESOURCE_TYPES)],
            'resource' => array_merge(['bail', 'required_with:resource_type'], $this->getResourceRules()),
        ], $this->getResourceItemRules());
    }

    protected function getResourceItemRules(): array
    {
        switch ($this->input('resource_type')) {
            case 'images':
                return [
                    'resource.*' => ['bail', 'required_with_all:resource_type,resource', 'distinct', 'string'],
                ];
            case 'link':
            case 'video':
            default:
                return [];
        }
    }

    protected function getResourceRules(): array
    {
        switch ($this->input('resource_type')) {
            case 'link':
                return ['url'];
            case 'images':
                return ['array'];
            case 'video':
                return ['bail', 'string'];
            default:
                return [];
        }
    }
}
