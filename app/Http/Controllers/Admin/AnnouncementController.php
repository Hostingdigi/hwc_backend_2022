<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcements;
use Session;

class AnnouncementController extends Controller
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
        $announcements = Announcements::paginate(25);
		return view('admin/Announcement.index', compact('announcements', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
        return view('admin/Announcement.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $announcement = new Announcements;
        $announcement->message = $request->message; 
        $announcement->display_order = $request->display_order;      	
		$announcement->status = $request->status;		
		$announcement->save();		
		return redirect('/admin/announcement')->with('success', 'Announcement Successfully Created!');
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
        $announcement = Announcements::where('id', '=', $id)->first();			
		return view('admin/Announcement.edit', compact('announcement'));
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
		
		Announcements::where('id', '=', $id)->update(array('message' => $request->message,'display_order' => $request->display_order, 'status' => $request->status));
		
		return redirect('/admin/announcement')->with('success', 'Announcement Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Announcements::where('id', '=', $id)->delete();
		return redirect('/admin/announcement')->with('success', 'Announcement Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Announcements::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/announcement')->with('success', 'Announcement Status Successfully Updated!');
	}
}
