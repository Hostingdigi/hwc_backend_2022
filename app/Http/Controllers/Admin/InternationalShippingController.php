<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InternationalShipping;
use App\Models\Country;
use Session;

class InternationalShippingController extends Controller
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
        $internationalshippings = InternationalShipping::paginate(25);
		$countries = Country::all();		
        return view('admin.internationalshipping.index', compact('internationalshippings','countries', 'moduleaccess', 'adminrole'));
    }
    public function create()
    {		
		$countries = Country::all();
        return view('admin/internationalshipping.create',compact('countries'));
    }
    public function store(Request $request)
    {		
        $internationalshipping = new InternationalShipping;
		$internationalshipping->Zone = $request->Zone;
		$internationalshipping->CountriesList = @implode(",",$request->CountryIds);
		$internationalshipping->ShipCost = $request->ShipCost;		
        $internationalshipping->PriceRange70 = $request->PriceRange70;
        $internationalshipping->PriceRange300 = $request->PriceRange300;
        $internationalshipping->PriceRange99999 = $request->PriceRange99999;
		$internationalshipping->FreeShippingCost = $request->FreeShippingCost;
		$internationalshipping->Status = $request->Status;
		$internationalshipping->save();		
		return redirect('/admin/international_shipping_methods')->with('success', 'International Shipping Method Successfully Created!');
    }
    public function edit($id)
    {
        $international_shipping_methods = InternationalShipping::where('Id', '=', $id)->first();	
		$countries = Country::all();	
		return view('admin/internationalshipping.edit', compact('international_shipping_methods','countries'));
    }

    public function update(Request $request)
    {
		
        $id = $request->id;
		/*$internationalshipping = InternationalShipping::find($id);        
        $internationalshipping->Zone = $request->Zone;
		$internationalshipping->CountriesList = @implode(",", $request->CountryIds);		
		$internationalshipping->ShipCost = $request->ShipCost;		
        $internationalshipping->PriceRange70 = $request->PriceRange70;
        $internationalshipping->PriceRange300 = $request->PriceRange300;
        $internationalshipping->PriceRange99999 = $request->PriceRange99999;
		$internationalshipping->Status = $request->Status;
		$internationalshipping->save();*/	
		
		InternationalShipping::where('Id', '=', $id)->update(array('Zone' => $request->Zone, 'CountriesList' => @implode(",", $request->CountryIds), 'ShipCost' => $request->ShipCost, 'PriceRange70' => $request->PriceRange70, 'PriceRange300' => $request->PriceRange300, 'PriceRange99999' => $request->PriceRange99999, 'Status' => $request->Status, 'FreeShippingCost' => $request->FreeShippingCost));
		
		return redirect('/admin/international_shipping_methods')->with('success', 'International Shipping Method Successfully Updated!');
    }


    public function destroy($id)
    {
        InternationalShipping::where('Id', '=', $id)->delete();
		return redirect('/admin/international_shipping_methods')->with('success', 'International Shipping Method Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		InternationalShipping::where('Id', '=', $id)->update(array('Status' => $statusval));
		
		return redirect('/admin/international_shipping_methods')->with('success', 'Group Status Successfully Updated!');
	}

}
