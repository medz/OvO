<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\ModelMorphMap;
use App\Models\ForumThread;
use App\Models\Talk;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateComment extends FormRequest
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
            'commentable_type' => ['bail', 'required', Rule::in([
                ModelMorphMap::classToAliasName(Talk::class),
                ModelMorphMap::classToAliasName(ForumThread::class),
            ])],
            'commentable_id' => ['bail', 'required', 'integer', 'min:1'],
            'content' => ['bail', 'required_without:resource_type', 'string', 'min:5', 'max:180'],
            'resource_type' => ['bail', 'required_without:content', 'required_with:resource', 'in:image,video,long-text'],
            'resource' => array_merge(['bail', 'required_with:resource_type'], $this->getResourceRules()),
        ], $this->getMergeItemRules());
    }

    protected function getMergeItemRules()
    {
        if ($this->resource_type === 'images') {
            return [
                'resource.*' => ['bail', 'required_with_all:resource_type,resource', 'distinct', 'string'],
            ];
        }

        return [];
    }

    protected function getResourceRules()
    {
        switch ($this->resource_type) {
            case 'long-text':
                return ['string', 'min: 200', 'max:5000'];
            case 'image':
                return ['array'];
            case 'video':
            default:
                return ['string'];
        }
    }
}
