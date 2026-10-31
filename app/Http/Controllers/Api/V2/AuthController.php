<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\ForgotPasswordRequest;
use App\Http\Requests\Api\V2\LoginRequest;
use App\Http\Requests\Api\V2\RegisterRequest;
use App\Http\Resources\V2\CustomerResource;
use App\Mail\TemplateMail;
use App\Models\Customer;
use App\Models\EmailTemplate;
use App\Models\Settings;
use App\Services\PasswordResets;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/** Sign-up, sign-in (Sanctum tokens), sign-out and password reset requests. */
class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $customer = new Customer;
        $customer->forceFill([
            'cust_firstname' => $request->first_name,
            'cust_lastname' => $request->last_name,
            'cust_email' => $request->email,
            'cust_username' => $request->email,
            'cust_password' => Hash::make($request->password),
            'cust_phone' => (string) $request->phone,
            'cust_company' => (string) $request->company,
            'cust_newsletter' => $request->boolean('newsletter') ? 1 : 0,
            'cust_status' => 1,
            'cust_type' => 1,
        ])->save();

        return $this->tokenResponse($customer->fresh(), $request->device_name, 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $customer = Customer::where(fn ($q) => $q->where('cust_email', $request->login)->orWhere('cust_username', $request->login))
            ->where('cust_status', 1)->first();

        if (!$customer || !Hash::check($request->password, (string) $customer->cust_password)) {
            return response()->json(['message' => 'The email or password is incorrect.'], 422);
        }

        return $this->tokenResponse($customer, $request->device_name);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    /** Always answers the same way, so it cannot be used to find out who has an account. */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $customer = Customer::where('cust_email', $request->email)->where('cust_status', 1)->first();
        $template = EmailTemplate::where('template_type', 6)->where('status', 1)->first();

        if ($customer && $template && $customer->password_reset_count < 3) {
            $customer->forceFill(['password_reset_count' => $customer->password_reset_count + 1])->save();
            $settings = Settings::where('id', 1)->first();
            $replace = [
                '{companyname}' => $settings->company_name ?? config('app.name'),
                '{customername}' => e(trim($customer->cust_firstname . ' ' . $customer->cust_lastname)),
                '{logo}' => '<img src="' . url('/img/logo.png') . '">',
                '{resetpasswordlink}' => '<a href="' . PasswordResets::linkFor($customer) . '">Reset Password</a>',
            ];
            Mail::to($customer->cust_email)->send(new TemplateMail(strtr($template->subject, $replace), strtr($template->content, $replace)));
        }

        return response()->json(['message' => 'If an account exists for that email, a reset link has been sent.']);
    }

    private function tokenResponse(Customer $customer, string $device, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $customer->createToken($device)->plainTextToken,
            'token_type' => 'Bearer',
            'customer' => new CustomerResource($customer),
        ], $status);
    }
}
