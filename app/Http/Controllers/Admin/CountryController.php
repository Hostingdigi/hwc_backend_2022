<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Session;

class CountryController extends Controller
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
        $countries = Country::paginate(25);
		return view('admin/Country.index', compact('countries', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
        return view('admin/Country.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $country = new Country;
		$country->countryname = $request->countryname;
		$country->countrycode = $request->countrycode;		
		$country->taxtitle = $request->taxtitle;		
		$country->taxpercentage = $request->taxpercentage;
		$country->country_status = $request->country_status;
		$country->save();		
		return redirect('/admin/country')->with('success', 'Country Successfully Created!');
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
        $country = Country::where('countryid', '=', $id)->first();			
		return view('admin/Country.edit', compact('country'));
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
		
		Country::where('countryid', '=', $id)->update(array('countryname' => $request->countryname, 'countrycode' => $request->countrycode, 'taxtitle' => $request->taxtitle, 'taxpercentage' => $request->taxpercentage, 'country_status' => $request->country_status));
		
		return redirect('/admin/country')->with('success', 'Country Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Country::where('countryid', '=', $id)->delete();
		return redirect('/admin/country')->with('success', 'Country Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Country::where('countryid', '=', $id)->update(array('country_status' => $statusval));
		
		return redirect('/admin/country')->with('success', 'Country Status Successfully Updated!');
	}
}
