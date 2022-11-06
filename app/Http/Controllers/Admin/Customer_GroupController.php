<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerGroup;


class Customer_GroupController extends Controller
{
    public function index()
    {       
        $customer_group = CustomerGroup::all();      
        return view('admin.Customer_Group.index', compact('customer_group'));
    }
    public function create()
    {
		$customer_group = CustomerGroup::all();		
        return view('admin/Customer_Group.create', compact('customer_group'));
    }
    public function store(Request $request)
    {
        $customer_group = new CustomerGroup;
		$customer_group->group_name = $request->group_name;
		$customer_group->discount_value = $request->discount_value;		
		$customer_group->type = $request->type;		
		$customer_group->DiscountType = $request->DiscountType;
		$customer_group->status = $request->status;
		$customer_group->save();		
		return redirect('/admin/Customer_Group')->with('success', 'Customer Group Successfully Created!');
    }
    public function edit($id)
    {
        $customer_group = CustomerGroup::find($id);			
		return view('admin/Customer_Group.edit', compact('customer_group'));
    }

    public function update(Request $request, Customer_Group $Customer_Group)
    {
        $id = $request->id;
		$Customer_Group = CustomerGroup::find($id);
        $Customer_Group->Country_name = $request->group_name;
		$Customer_Group->country_code = $request->discount_value;		
		$Customer_Group->tax_title = $request->type;		
		$Customer_Group->tax_percentage = $request->DiscountType;
		$Customer_Group->status = $request->status;		
		$Customer_Group->save();		
		return redirect('/admin/customer_group')->with('success', 'Country Successfully Updated!');
    }


    public function destroy($id)
    {
        CustomerGroup::where('id', '=', $id)->delete();
		return redirect('/admin/customer_group')->with('success', 'Customer Group Successfully Deleted!');
    }

}
