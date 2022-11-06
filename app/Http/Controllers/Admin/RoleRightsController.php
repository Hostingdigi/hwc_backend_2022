<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoleRights;
use App\Models\Modules;
use Session;

class RoleRightsController extends Controller
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
        $rolerights = RoleRights::paginate(25);
		return view('admin/RoleRights.index', compact('rolerights', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
		$modules = Modules::where('parent_id', '=', '0')->get();
        return view('admin/RoleRights.create', compact('modules'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
		//print_r($request->all()); exit;
		
        $rolerights = new RoleRights;
        $rolerights->role_name = $request->role_name; 
		$rights = '';
		if(isset($request->rights)) {
			$rights = @implode(',', $request->rights);
		}		
        $rolerights->rights = $rights;		
		$rolerights->save();		
		return redirect('/admin/roleandrights')->with('success', 'Role Rights Successfully Created!');
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
		$modules = Modules::where('parent_id', '=', '0')->get();
        $rolerights = RoleRights::where('id', '=', $id)->first();			
		return view('admin/RoleRights.edit', compact('rolerights', 'modules'));
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
		$rights = '';
		if(isset($request->rights)) {
			$rights = @implode(',', $request->rights);
		}		
        
		RoleRights::where('id', '=', $id)->update(array('role_name' => $request->role_name,'rights' => $rights));
		
		return redirect('/admin/roleandrights')->with('success', 'Role Rights Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        RoleRights::where('id', '=', $id)->delete();
		return redirect('/admin/roleandrights')->with('success', 'Role Rights Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		RoleRights::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/roleandrights')->with('success', 'Role Rights Status Successfully Updated!');
	}
}
