<?php

namespace App\Services;

use RuntimeException;

/** Why an order could not be placed; $reason is a stable code for API clients. */
class OrderPlacementException extends RuntimeException
{
    public function __construct(string $message, public string $reason, public ?array $quote = null)
    {
        parent::__construct($message);
    }
}
