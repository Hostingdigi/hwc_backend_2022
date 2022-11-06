<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\PaymentSettings;

class PaymentSettingsController extends Controller
{
    public function index()
    {
        $paysettings = PaymentSettings::where('id', '=', '1')->first();
		return view('admin/paymentsettings.index', compact('paysettings'));
    }   
    public function update(Request $request)
    {
        $id = $request->id;
		
		PaymentSettings::where('id', '=', $id)->update(array('paypal_url' => $request->paypal_url, 'paypal_email' => $request->paypal_email, 'paypal_max_amount'=> $request->paypal_max_amount, 'auth_max_amount' => $request->auth_max_amount, 'currency_type' => $request->currency_type, 'free_shipping_amount' => $request->free_shipping_amount, 'shipping_cost' => $request->shipping_cost, 'discount_percentage' => $request->discount_percentage, 'promo_discount_percentage' => $request->promo_discount_percentage, 'P_package_fee' => $request->P_package_fee, 'XXL_package_fee' => $request->XXL_package_fee, 'XL_package_fee' => $request->XL_package_fee, 'L_package_fee' => $request->L_package_fee, 'M_package_fee' => $request->M_package_fee, 'S_package_fee' => $request->S_package_fee, 'XS_package_fee' => $request->XS_package_fee, 'XXS_package_fee' => $request->XXS_package_fee, 'min_package_fee' => $request->min_package_fee, 'local_free_shipping_msg' => $request->local_free_shipping_msg, 'international_free_shipping_msg' => $request->international_free_shipping_msg, 'quotation_expiry_day' => $request->quotation_expiry_day));
		
		return redirect('/admin/payment_settings')->with('success', 'Payment Settings Successfully Updated!');
    }
}