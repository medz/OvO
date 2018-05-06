<?php

declare(strict_types=1);

namespace App\Api\Requests;

class UploadFileRequest extends Request
{
    /**
     * Get the valition rules of the request.
     * 
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|file',
        ];
    }

    /**
     * Get the valition messages of the request.
     * 
     * @return array
     * @author Seven Du <shiwidu@outlook.com>
     */
    public function messages(): array
    {
        return [];
    }
}
