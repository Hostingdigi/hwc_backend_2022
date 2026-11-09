<?php

namespace App\Http\Requests;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Storefront customer sign-in (web guard). Customers may sign in with either
 * their username or their email address.
 */
class CustomerLoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS = 5;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate on the "web" guard, throttled per username + IP.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $username = $this->string('username')->trim()->toString();

        $credentials = [
            fn (Builder $query) => $query->where(
                fn (Builder $q) => $q->where('cust_username', $username)->orWhere('cust_email', $username)
            ),
            'password' => $this->string('password')->toString(),
        ];

        // customer_master has no remember_token column, so "remember me" is not supported.
        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'Invalid Login Credentials!',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => 'Too many login attempts. Please try again in ' . ceil($seconds / 60) . ' minute(s).',
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')->toString()) . '|' . $this->ip());
    }
}
