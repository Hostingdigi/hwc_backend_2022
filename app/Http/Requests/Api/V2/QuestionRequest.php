<?php

namespace App\Http\Requests\Api\V2;

/** Ask a question about a product. */
class QuestionRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'question' => 'required|string|max:2000',
        ];
    }
}
