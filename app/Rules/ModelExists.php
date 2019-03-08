<?php

declare(strict_types=1);

namespace App\Rules;

use App\ModelMorphMap;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Rule;

class ModelExists implements Rule
{
    protected $request;
    protected $aliasInput;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Request $request, string $aliasInput)
    {
        $this->request = $request;
        $this->aliasInput = $aliasInput;
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
        return call_user_func([ModelMorphMap::aliasToClassName($this->request->__get($this->aliasInput)) ?: $this->aliasInput, 'find'], $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('分享的内容不存在！');
    }
}
