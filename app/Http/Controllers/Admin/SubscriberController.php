<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Session;

class SubscriberController extends Controller
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
        $subscribers = Subscriber::orderBy('id', 'desc')->paginate(50);
        return view('admin.Subsriber.index', compact('subscribers', 'moduleaccess', 'adminrole'));
        /*echo "<pre>";
        print_r($subscriber);
        die;*/
    }
    public function Subscriber()
    {
        $subscriber = Subscriber::all();
        return view('admin/Subscriber/index', compact('subscriber'));
        /*echo "<pre>";
        print_r($subscriber);
        die;*/
    }
    public function create()
    {
		$subscriber = Subscriber::all();		
        return view('admin.Subsriber.add', compact('subscriber'));
    }
    public function store(Request $request)
    {
        $subscriber = new Subscriber;
		$subscriber->name = $request->name;
		$subscriber->email = $request->email;		
		$subscriber->ContactNo = $request->ContactNo;				
		$subscriber->status = $request->status;
        $subscriber->save();	        
		return redirect('/admin/subscriber')->with('success', 'Subscriber Successfully Created!');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $subscriber = Subscriber::find($id);		
		return view('admin.Subsriber.edit', compact('subscriber'));
    }
    public function update(Request $request, Subscriber $subscriber)
    {
        $id = $request->id;
		$subscriber = Subscriber::find($id);
        $subscriber->name = $request->name;
		$subscriber->email = $request->email;		
		$subscriber->ContactNo = $request->ContactNo;				
		$subscriber->status = $request->status;		
		$subscriber->save();		
		return redirect('/admin/subscriber')->with('success', 'Subscriber Successfully Updated!');
    }
    public function destroy($id)
    {
        Subscriber::where('id', '=', $id)->delete();
		return redirect('/admin/subscriber')->with('success', 'Subscriber Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Subscriber::where('id', '=', $id)->update(array('status' => $statusval));		
		return redirect('/admin/subscriber')->with('success', 'Subscriber Status Successfully Updated!');
	}	
}