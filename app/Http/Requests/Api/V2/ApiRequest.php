<?php

namespace App\Http\Requests\Api\V2;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Base for API v2 requests. Who may call an endpoint is decided by route
 * middleware (auth:sanctum); requests only validate input. Validation errors
 * come back as 422 JSON (see bootstrap/app.php).
 */
abstract class ApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
