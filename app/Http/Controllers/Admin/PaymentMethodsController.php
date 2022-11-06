<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentMethods;
use Session;

class PaymentMethodsController extends Controller
{
    public function index()
    {
		$adminrole = 0;
		$moduleaccess = [];
		if(Session::has('accessrights')) {
			$moduleaccess = Session::get('accessrights');
		}
		
		if(Session::has('priority')) {
			$adminrole = Session::get('priority');
		}
        $paymentmethods = PaymentMethods::paginate(25);
        return view('admin.PaymentMethods.index', compact('paymentmethods', 'moduleaccess', 'adminrole'));
        /*echo "<pre>";
        print_r($paymentmethods);
        die;
    }
    public function PaymentMethods()
    {
        $paymentmethods = PaymentMethods::all();
        return view('admin/PaymentMethods/index', compact('paymentmethods'));
        /*echo "<pre>";
        print_r($paymentmethods);
        die;*/
    }
    public function create()
    {
		$paymentmethods = PaymentMethods::all();		
        return view('admin.PaymentMethods.add', compact('paymentmethods'));
    }
    public function store(Request $request)
    {
        $paymentmethods = new PaymentMethods;
		$paymentmethods->payment_mode = $request->payment_mode;
		$paymentmethods->payment_name = $request->payment_name;		
		$paymentmethods->testing_url = $request->testing_url;
		$paymentmethods->test_api_key = $request->test_api_key;		
		$paymentmethods->test_api_signature = $request->test_api_signature;	
		$paymentmethods->live_url = $request->live_url;		
		$paymentmethods->api_key = $request->api_key;		
		$paymentmethods->api_signature = $request->api_signature;				
		$paymentmethods->email = $request->email;				
		$paymentmethods->status = $request->status;
        $paymentmethods->save();	        
		return redirect('/admin/paymentmethods')->with('success', 'PaymentMethods Successfully Created!');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $paymentmethods = PaymentMethods::find($id);		
		return view('admin.PaymentMethods.edit', compact('paymentmethods'));
    }
    public function update(Request $request, PaymentMethods $paymentmethods)
    {
        $id = $request->id;

		PaymentMethods::Where('Id', '=', $id)->update(array('payment_mode' => $request->payment_mode, 'payment_name' => $request->payment_name, 'testing_url' => $request->testing_url, 'test_api_key' => $request->test_api_key, 'test_api_signature' => $request->test_api_signature, 'live_url'=> $request->live_url,'api_key' => $request->api_key,'api_signature' => $request->api_signature,'email' => $request->email, 'status' => $request->status));		
		return redirect('/admin/paymentmethods')->with('success', 'PaymentMethods Successfully Updated!');
    }
    public function destroy($id)
    {
        PaymentMethods::where('Id', '=', $id)->delete();
		return redirect('/admin/paymentmethods')->with('success', 'PaymentMethods Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		PaymentMethods::where('Id', '=', $id)->update(array('status' => $statusval));		
		return redirect('/admin/paymentmethods')->with('success', 'PaymentMethods Status Successfully Updated!');
	}	
}