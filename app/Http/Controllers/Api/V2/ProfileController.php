<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V2\ChangePasswordRequest;
use App\Http\Requests\Api\V2\ConfirmDeactivationRequest;
use App\Http\Requests\Api\V2\UpdateProfileRequest;
use App\Http\Resources\V2\CustomerResource;
use App\Mail\TemplateMail;
use App\Models\AccountDeactiveRequest;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/** The signed-in customer's own account. */
class ProfileController extends Controller
{
    public function show(Request $request): CustomerResource
    {
        return new CustomerResource($request->user());
    }

    public function update(UpdateProfileRequest $request): CustomerResource
    {
        $map = [
            'first_name' => 'cust_firstname', 'last_name' => 'cust_lastname', 'phone' => 'cust_phone',
            'company' => 'cust_company', 'address1' => 'cust_address1', 'address2' => 'cust_address2',
            'city' => 'cust_city', 'state' => 'cust_state', 'postcode' => 'cust_zip',
        ];
        $changes = [];
        foreach ($map as $field => $column) {
            if ($request->has($field)) {
                $changes[$column] = (string) $request->input($field);
            }
        }
        if ($request->filled('country')) {
            $changes['cust_country'] = (int) Country::where('countrycode', $request->country)->value('countryid');
        }

        $customer = $request->user();
        $customer->forceFill($changes)->save();

        return new CustomerResource($customer->fresh());
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $customer = $request->user();
        if (!Hash::check($request->current_password, (string) $customer->cust_password)) {
            return response()->json(['message' => 'The current password is incorrect.', 'errors' => ['current_password' => ['The current password is incorrect.']]], 422);
        }

        $customer->forceFill(['cust_password' => Hash::make($request->password)])->save();
        // Sign out every other device
        $customer->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Password changed.']);
    }

    /** Emails a 6-digit code that confirmDeactivation() needs (same flow as v1). */
    public function requestDeactivation(Request $request): JsonResponse
    {
        $customer = $request->user();
        $otp = (string) random_int(100000, 999999);

        AccountDeactiveRequest::where('user_id', $customer->cust_id)->where('status', 1)->update(['status' => 3]);
        AccountDeactiveRequest::create([
            'user_id' => $customer->cust_id, 'date_time' => now(), 'otp_number' => $otp, 'status' => 1,
            'description' => (string) $request->input('reason', ''),
        ]);

        $html = '<p>Hi ' . e(trim($customer->cust_firstname . ' ' . $customer->cust_lastname)) . ',</p><p>Your code to delete your account is <b>' . $otp . '</b>.</p>';
        Mail::to($customer->cust_email)->send(new TemplateMail('OTP for account deactivation', $html));

        return response()->json(['message' => 'A confirmation code has been emailed to you.']);
    }

    public function confirmDeactivation(ConfirmDeactivationRequest $request): JsonResponse
    {
        $customer = $request->user();
        $pending = AccountDeactiveRequest::where(['user_id' => $customer->cust_id, 'status' => 1])
            ->where('created_at', '>=', now()->subHour())->latest('id')->first();

        if (!$pending || !hash_equals((string) $pending->otp_number, (string) $request->otp)) {
            return response()->json(['message' => 'The code is invalid or has expired.', 'errors' => ['otp' => ['The code is invalid or has expired.']]], 422);
        }

        $pending->update(['status' => 2]);
        $customer->forceFill(['cust_status' => 0])->save();
        $customer->tokens()->delete();

        Mail::to($customer->cust_email)->send(new TemplateMail('Account Deactivated',
            '<p>Hi ' . e(trim($customer->cust_firstname . ' ' . $customer->cust_lastname)) . '</p><p>Your account has been deactivated.</p>'));

        return response()->json(['message' => 'Your account has been deactivated.']);
    }
}
