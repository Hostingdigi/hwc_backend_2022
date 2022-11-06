<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;
use Session;

class CustomerGroupController extends Controller
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
        $customergroups = CustomerGroup::orderBy('Id', 'desc')->paginate(25);      
        return view('admin.CustomerGroup.index', compact('customergroups', 'moduleaccess', 'adminrole'));
    }
    public function create()
    {			
        return view('admin/CustomerGroup.create');
    }
    public function store(Request $request)
    {		
        $customer_group = new CustomerGroup;
		$customer_group->GroupTitle = $request->GroupTitle;
		$customer_group->DiscountValue = $request->DiscountValue;		
		$customer_group->type = $request->type;		
		$customer_group->DiscountType = $request->DiscountType;
		$customer_group->Status = $request->Status;
		$customer_group->save();		
		return redirect('/admin/customergroup')->with('success', 'Customer Group Successfully Created!');
    }
    public function edit($id)
    {
        $customergroup = CustomerGroup::where('Id', '=', $id)->first();			
		return view('admin/CustomerGroup.edit', compact('customergroup'));
    }

    public function update(Request $request)
    {
        $id = $request->id;
		
		CustomerGroup::where('Id', '=', $id)->update(array('GroupTitle' => $request->GroupTitle, 'DiscountValue' => $request->DiscountValue, 'type' => $request->type, 'DiscountType' => $request->DiscountType, 'Status' => $request->Status));
		
		return redirect('/admin/customergroup')->with('success', 'Customer Group Successfully Updated!');
    }


    public function destroy($id)
    {
        CustomerGroup::where('Id', '=', $id)->delete();
		return redirect('/admin/customergroup')->with('success', 'Customer Group Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		CustomerGroup::where('Id', '=', $id)->update(array('Status' => $statusval));
		
		return redirect('/admin/customergroup')->with('success', 'Group Status Successfully Updated!');
	}

}
