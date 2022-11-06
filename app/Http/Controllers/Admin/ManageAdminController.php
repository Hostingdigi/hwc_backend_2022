<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManageAdmin;
use App\Models\RoleRights;
use Hash;
use Session;

class ManageAdminController extends Controller
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
        $manageadmins = ManageAdmin::where('priority', '>', '0')->paginate(25);
		return view('admin.manageadmin.index', compact('manageadmins', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$rolerights = RoleRights::all();
        return view('admin.manageadmin.create', compact('rolerights'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $manageadmin = new ManageAdmin;
		$manageadmin->name = $request->admin_uname;
		$manageadmin->username = $request->admin_uname;
		$manageadmin->password = Hash::make($request->admin_password);		
		$manageadmin->priority = $request->priority;				
		$manageadmin->save();		
		return redirect('/admin/manageadmin')->with('success', 'Admin Successfully Created!');
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
		$rolerights = RoleRights::all();
        $manageadmin = ManageAdmin::where('id', '=', $id)->first();			
		return view('admin.manageadmin.edit', compact('manageadmin', 'rolerights'));
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
		
		$password = '';
		if(isset($request->admin_password)) {
			$password = Hash::make($request->admin_password);
			ManageAdmin::where('id', '=', $id)->update(array('name' => $request->admin_uname, 'username' => $request->admin_uname, 'password' => $request->admin_password, 'priority' => $request->priority));
		} else {
			ManageAdmin::where('id', '=', $id)->update(array('name' => $request->admin_uname, 'username' => $request->admin_uname, 'priority' => $request->priority));
		}
		
		return redirect('/admin/manageadmin')->with('success', 'Admin Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ManageAdmin::where('id', '=', $id)->delete();
		return redirect('/admin/manageadmin')->with('success', 'Admin Successfully Deleted!');
    }	
}
