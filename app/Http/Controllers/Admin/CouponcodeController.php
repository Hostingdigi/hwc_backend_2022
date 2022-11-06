<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Couponcode;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use Session;

class CouponcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        $couponcodes = Couponcode::orderBy('id', 'desc')->paginate(25);
        $brands = Brand::all();	
		$categories = Category::where('ParentLevel', '=', 0)->get();
		
		return view('admin/Couponcode.index', compact('couponcodes','brands','categories', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {	
        $couponcode = Couponcode::all();
        $brands = Brand::all();	
        $categories = Category::where('ParentLevel', '=', 0)->get();
		$customers = Customer::where('cust_status', '=', '1')->orderBy('cust_firstname', 'asc')->get();
        /*$voucher->code = $this->generateRandomString(6);	*/
        return view('admin/Couponcode.create', compact('couponcode','brands','categories', 'customers'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $couponcode = new Couponcode;
        $couponcode->coupon_code = $request->coupon_code; 
		$couponcode->customer_id = $request->customer_id; 
        $couponcode->category_id = $request->category_id; 
        $couponcode->brand_id = $request->brand_id; 
        $couponcode->validity = $request->validity; 
        $couponcode->nooftimes = $request->nooftimes; 
        $couponcode->discount_type = $request->discount_type; 
        $couponcode->discount = $request->discount; 
        $couponcode->customer_type = $request->customer_type;      	
		$couponcode->status = $request->status;		
		$couponcode->save();		
		return redirect('/admin/couponcode')->with('success', 'Couponcode Successfully Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {		
        $couponcode = Couponcode::where('id', '=', $id)->first();	
        $brands = Brand::all();	
        $categories = Category::where('ParentLevel', '=', 0)->get();	
		$customers = Customer::where('cust_status', '=', '1')->orderBy('cust_firstname', 'asc')->get();	
		return view('admin/Couponcode.edit', compact('couponcode','brands','categories', 'customers'));
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
		
        Couponcode::where('id', '=', $id)->update(array('coupon_code' => $request->coupon_code, 'customer_id' => $request->customer_id, 'category_id' => $request->category_id, 'brand_id' => $request->brand_id, 'validity' => $request->validity, 'nooftimes' => $request->nooftimes, 'discount_type' => $request->discount_type, 'discount' => $request->discount, 'customer_type' => $request->customer_type,'status' => $request->status));
		
		return redirect('/admin/couponcode')->with('success', 'Couponcode Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Couponcode::where('id', '=', $id)->delete();
		return redirect('/admin/couponcode')->with('success', 'Couponcode Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Couponcode::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/couponcode')->with('success', 'Couponcode Status Successfully Updated!');
	}
}
