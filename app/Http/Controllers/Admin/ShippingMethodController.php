<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethods;
use Illuminate\Http\Request;
use Session;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }
        $shippingmethods = ShippingMethods::paginate(25);
        return view('admin.ShippingMethods.index', compact('shippingmethods', 'moduleaccess', 'adminrole'));
    }
    public function create()
    {
        $displayorder = 1;
        $shippingmethods = ShippingMethods::select('DisplayOrder')->orderBy('Id', 'desc')->first();
        if ($shippingmethods) {
            $displayorder = $shippingmethods->DisplayOrder + 1;
        }
        return view('admin/ShippingMethods.create', compact('displayorder'));
    }
    public function store(Request $request)
    {
        $shippingmethod = new ShippingMethods;
        $shippingmethod->EnName = $request->EnName;
        $shippingmethod->UniqueKey = $request->UniqueKey;
        $shippingmethod->Price = $request->Price;
        if (isset($request->FreeShipAvailable)) {
            $shippingmethod->FreeShipAvailable = $request->FreeShipAvailable;
        } else {
            $shippingmethod->FreeShipAvailable = 0;
        }
        $shippingmethod->FreeShipCost = $request->FreeShipCost;
        $shippingmethod->Status = $request->Status;
        $shippingmethod->shipping_type = $request->shipping_type;
        $shippingmethod->DisplayOrder = $request->DisplayOrder;
        $shippingmethod->save();
        return redirect('/admin/shipping_methods')->with('success', 'Shipping Method Successfully Created!');
    }
    public function edit($id)
    {
        $shippingmethods = ShippingMethods::where('Id', '=', $id)->first();
        return view('admin/ShippingMethods.edit', compact('shippingmethods'));
    }

    public function update(Request $request)
    {
        $id = $request->id;

        if (isset($request->FreeShipAvailable)) {
            $FreeShipAvailable = $request->FreeShipAvailable;
        } else {
            $FreeShipAvailable = 0;
        }

        ShippingMethods::where('Id', '=', $id)->update(array('EnName' => $request->EnName, 'UniqueKey' => $request->UniqueKey, 'Price' => $request->Price, 'FreeShipCost' => $request->FreeShipCost, 'FreeShipAvailable' => $FreeShipAvailable, 'Status' => $request->Status, 'shipping_type' => $request->shipping_type, 'DisplayOrder' => $request->DisplayOrder));

        return redirect('/admin/shipping_methods')->with('success', 'Shipping Method Successfully Updated!');
    }

    public function destroy($id)
    {
        ShippingMethods::where('Id', '=', $id)->delete();
        return redirect('/admin/shipping_methods')->with('success', 'Shipping Method Successfully Deleted!');
    }

    public function updatestatus($id, $status)
    {
        $statusval = 1;
        if ($status == 1) {
            $statusval = 0;
        }
        ShippingMethods::where('Id', '=', $id)->update(array('Status' => $statusval));

        return redirect('/admin/shipping_methods')->with('success', 'Shipping Method Successfully Updated!');
    }

}
