<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\local_shipping_method;

class Local_ShippingController extends Controller
{
    public function index()
    {
        $Local_shipping_method = Local_shipping_method::all();
		return view('admin/Local_Shipping.index', compact('Local_shipping_method'));
    }

    public function store(Request $request)
    {
        $Local_shipping_method = new Local_shipping_method;
		$Local_shipping_method->EnName = $request->delivery_type;
		$Local_shipping_method->hint = $request->hint;		
		$Local_shipping_method->PriceRange1 = $request->PriceRange1;		
        $Local_shipping_method->PriceRange2 = $request->PriceRange2;
        $Local_shipping_method->PriceRange5 = $request->PriceRange5;
        $Local_shipping_method->PriceRange10 = $request->PriceRange10;
        $Local_shipping_method->FreeShipAvailable = $request->FreeShipAvailable;
        $Local_shipping_method->FreeShipCost = $request->FreeShipCost;
		$Local_shipping_method->status = $request->status;
		$Local_shipping_method->save();		
		return redirect('/admin/local_shipping')->with('success', 'Local Shipping Method Successfully Created!');
    }
    public function create()
    {
		$Local_shipping_method = Local_shipping_method::all();		
        return view('admin/Local_shipping.create', compact('Local_shipping_method'));
    }
    public function edit($id)
    {
        $Local_shipping_method = Local_shipping_method::find($id);			
		return view('admin/Local_shipping.edit', compact('Local_shipping_method'));
    }
    public function update(Request $request, Local_shipping_method $Local_shipping_method)
    {
        $id = $request->id;
		$Local_shipping_method = Local_shipping_method::find($id);
        $Local_shipping_method = new Local_shipping_method;
		$Local_shipping_method->EnName = $request->delivery_type;
		$Local_shipping_method->hint = $request->hint;		
		$Local_shipping_method->PriceRange1 = $request->PriceRange1;		
        $Local_shipping_method->PriceRange2 = $request->PriceRange2;
        $Local_shipping_method->PriceRange5 = $request->PriceRange5;
        $Local_shipping_method->PriceRange10 = $request->PriceRange10;
        $Local_shipping_method->FreeShipAvailable = $request->FreeShipAvailable;
        $Local_shipping_method->FreeShipCost = $request->FreeShipCost;
		$Local_shipping_method->status = $request->status;	
		$Local_shipping_method->save();		
		return redirect('/admin/Local_shipping')->with('success', 'Local shipping Method Successfully Updated!');
    }
    public function destroy($id)
    {
        Subscriber::where('id', '=', $id)->delete();
		return redirect('/admin/Local_shipping')->with('success', 'Local shipping Method Successfully Deleted!');
    }
    
}
