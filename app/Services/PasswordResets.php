<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\Password;

/**
 * Customer password reset links backed by Laravel's password broker.
 *
 * The old link was /resetpassword/{base64(email)} and the form posted the
 * customer id, so anyone could reset any customer's password. Links now carry
 * a random single-use token (stored hashed in password_resets, valid for
 * auth.passwords.customers.expire minutes).
 */
class PasswordResets
{
    public static function linkFor(Customer $customer): string
    {
        $token = Password::broker('customers')->createToken($customer);

        return url('/resetpassword/' . $token) . '?email=' . urlencode($customer->cust_email);
    }

    /**
     * Sets the new password if the token is valid for that email. Returns false otherwise.
     */
    public static function reset(string $email, string $token, string $newPassword): bool
    {
        $broker = Password::broker('customers');
        $customer = Customer::where('cust_email', $email)->first();

        if (!$customer || !$broker->tokenExists($customer, $token)) {
            return false;
        }

        $customer->forceFill(['cust_password' => bcrypt($newPassword), 'password_reset_count' => 0])->save();
        $broker->deleteToken($customer);

        return true;
    }
}
