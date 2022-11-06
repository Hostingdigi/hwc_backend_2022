<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Country;
use App\Models\CustomerGroup;
use Hash;
use Session;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
		$sortby = 'cust_id';
		$sortorder = 'desc';
		$status = $request->status;
		$cust_type = $request->cust_type;
		$filter_column = $request->filter_column;
		$Nametype = $request->Nametype;
		$filter_srch_val = $request->filter_srch_val;
		$sort_column = $request->sort_column;
		
		$join = 'cust_id > 0';
		if($status != '') {
			$join .= ' AND cust_status = '.$status;
		}
		if($cust_type != '') {
			$join .= ' AND cust_type = '.$cust_type;
		}
		if($filter_srch_val != '') {
			if($Nametype == '1') {
				$join .= ' AND '.$filter_column.' = '.$filter_srch_val;
			} else {
				$join .= ' AND '.$filter_column.' LIKE "%'.$filter_srch_val.'%"';
			}
		}
		if($sort_column > 0) {
			if($sort_column == '1') {
				$sortby = 'cust_firstname';
				$sortorder = 'ASC';
			} elseif($sort_column == '2') {
				$sortby = 'cust_firstname';
				$sortorder = 'DESC';
			} elseif($sort_column == '3') {
				$sortby = 'cust_lastname';
				$sortorder = 'ASC';
			} elseif($sort_column == '4') {
				$sortby = 'cust_lastname';
				$sortorder = 'DESC';
			}
		}
		
        $customers = Customer::whereRaw($join)->orderBy($sortby, $sortorder)->paginate(50);
		$groups = CustomerGroup::orderBy('Id', 'asc')->get();

		$adminrole = 0;
		$moduleaccess = [];
		if(Session::has('accessrights')) {
			$moduleaccess = Session::get('accessrights');
		}
		
		if(Session::has('priority')) {
			$adminrole = Session::get('priority');
		}
			
        return view('admin.customer.index', compact('customers', 'groups', 'status', 'cust_type', 'filter_column', 'Nametype', 'filter_srch_val', 'sort_column', 'moduleaccess', 'adminrole'));
    }
    public function create()
    {
		$countries = Country::where('country_status', '=', '1')->get();
		$groups = CustomerGroup::where('Status', '=', '1')->orderBy('Id', 'asc')->get();	
        return view('admin.customer.create', compact('countries', 'groups'));
    }
    public function store(Request $request)
    {
        $customer = new Customer;		
		$customer->cust_firstname = $request->cust_firstname;		
		$customer->cust_lastname = $request->cust_lastname;		
        $customer->cust_email = $request->cust_email;
        $customer->cust_username = $request->cust_username;
        $customer->cust_password = Hash::make($request->cust_password);
        $customer->cust_dob = $request->cust_dob;
        $customer->cust_type = $request->cust_type;
		$groupadmin = 0;
		if(isset($request->group_admin)) {
			$groupadmin = $request->group_admin;
		}
		$customer->group_admin = $groupadmin;
        $customer->cust_address1 = $request->cust_address1;
        $customer->cust_address2 = $request->cust_address2;
        $customer->cust_city = $request->cust_city;
        $customer->cust_state = $request->cust_state;
        $customer->cust_country = $request->cust_country;
        $customer->cust_zip = $request->cust_zip;
        $customer->cust_phone = $request->cust_phone;
        $customer->cust_landline = $request->cust_landline;
        $customer->cust_office = $request->cust_office;
		$newsletter = 0;
		if(isset($request->cust_newsletter)) {
			$newsletter = $request->cust_newsletter;
		}
        $customer->cust_newsletter = $newsletter;
		$customer->cust_status = $request->cust_status;
		$customer->save();		
		return redirect('/admin/customer')->with('success', 'Customer Successfully Created!');
    }
    public function edit($id)
    {
		$countries = Country::where('country_status', '=', '1')->get();
        $customer = Customer::where('cust_id', '=', $id)->first();
		$groups = CustomerGroup::where('Status', '=', '1')->orderBy('Id', 'asc')->get();		
		return view('admin/customer.edit', compact('customer', 'countries', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
		$groupadmin = 0;
		if(isset($request->group_admin)) {
			$groupadmin = $request->group_admin;
		}
		$newsletter = 0;
		if(isset($request->cust_newsletter)) {
			$newsletter = $request->cust_newsletter;
		}
		Customer::where('cust_id', '=', $id)->update(array('cust_firstname' => $request->cust_firstname, 'cust_lastname' => $request->cust_lastname, 'cust_email' => $request->cust_email, 'cust_username' => $request->cust_username, 'cust_type' => $request->cust_type, 'group_admin' => $groupadmin, 'cust_address1' => $request->cust_address1, 'cust_address2' => $request->cust_address2, 'cust_city' => $request->cust_city, 'cust_state' => $request->cust_state, 'cust_country' => $request->cust_country, 'cust_zip' => $request->cust_zip, 'cust_phone' => $request->cust_phone, 'cust_landline' => $request->cust_landline, 'cust_office' => $request->cust_office, 'cust_newsletter' => $newsletter, 'cust_status' => $request->cust_status));
		
		return redirect('/admin/customer')->with('success', 'Customer Successfully Updated!');
    }

    public function destroy($id)
    {
        Customer::where('cust_id', '=', $id)->delete();
		return redirect('/admin/customer')->with('success', 'Customer Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Customer::where('cust_id', '=', $id)->update(array('cust_status' => $statusval));		
		return redirect('/admin/customer')->with('success', 'Customer Status Successfully Updated!');
	}

}
