<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\LocalShipping;
use Session;

class LocalShippingController extends Controller
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
        $Local_shipping_methods = Localshipping::paginate(25);
		return view('admin/localshipping.index', compact('Local_shipping_methods', 'moduleaccess', 'adminrole'));
    }

    public function store(Request $request)
    {
        $Local_shipping_method = new Localshipping;
		$Local_shipping_method->EnName = $request->EnName;
		$Local_shipping_method->Hint = $request->Hint;		
		$Local_shipping_method->PriceRange5 = $request->PriceRange5;		
        $Local_shipping_method->PriceRange15 = $request->PriceRange15;
        $Local_shipping_method->PriceRange30 = $request->PriceRange30;
        $Local_shipping_method->PriceRangeAbove30 = $request->PriceRangeAbove30;
        $Local_shipping_method->FreeShipAvailable = $request->FreeShipAvailable;
        $Local_shipping_method->FreeShipCost = $request->FreeShipCost;
        $Local_shipping_method->status = $request->Status;
        $Local_shipping_method->WeightFrom = 1;
		$Local_shipping_method->save();		
		return redirect('/admin/local_shipping_methods')->with('success', 'Local Shipping Method Successfully Created!');
    }
    public function create()
    {
		$Local_shipping_method = Localshipping::all();		
        return view('admin/localshipping.create', compact('Local_shipping_method'));
    }
    public function edit($id)
    {
        $Local_shipping_method = Localshipping::find($id);			
        return view('admin/localshipping.edit', compact('Local_shipping_method'));       
    }
    public function update(Request $request)
    {
        $id = $request->id;
				
		LocalShipping::where('Id', '=', $id)->update(array('EnName' => $request->EnName, 'Hint' => $request->Hint, 'PriceRange5' => $request->PriceRange5, 'PriceRange15' => $request->PriceRange15, 'PriceRange30' => $request->PriceRange30, 'PriceRangeAbove30' => $request->PriceRangeAbove30, 'FreeShipCost' => $request->FreeShipCost, 'Status' => $request->Status));
		
		return redirect('/admin/local_shipping_methods')->with('success', 'Local shipping Method Successfully Updated!');
    }
    public function destroy($id)
    {
        Localshipping::where('Id', '=', $id)->delete();
		return redirect('/admin/local_shipping_methods')->with('success', 'Local shipping Method Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Localshipping::where('Id', '=', $id)->update(array('Status' => $statusval));		
		return redirect('/admin/local_shipping_methods')->with('success', 'Local shipping Method Status Successfully Updated!');
	}	
    
}
