<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailTemplate;
use Session;

class EmailTemplateController extends Controller
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
        $emailtemplates = EmailTemplate::paginate(25);
		return view('admin/EmailTemplate.index', compact('emailtemplates', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
        return view('admin/EmailTemplate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $emailtemplate = new EmailTemplate;
        $emailtemplate->templatename = $request->templatename;	
        $emailtemplate->subject = $request->subject;	
        $emailtemplate->content = $request->content;		
		$emailtemplate->status = $request->status;
		$emailtemplate->template_type = $request->template_type;
		$emailtemplate->save();		
		return redirect('/admin/emailtemplate')->with('success', 'Email Template Successfully Created!');
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
        $emailtemplate = EmailTemplate::where('id', '=', $id)->first();			
		return view('admin/EmailTemplate.edit', compact('emailtemplate'));
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
		
		EmailTemplate::where('id', '=', $id)->update(array('templatename' => $request->templatename,'subject' => $request->subject, 'content' => $request->content,  'status' => $request->status, 'template_type' => $request->template_type));
		
		return redirect('/admin/emailtemplate')->with('success', 'Email Template Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        EmailTemplate::where('id', '=', $id)->delete();
		return redirect('/admin/emailtemplate')->with('success', 'Email Template Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		EmailTemplate::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/emailtemplate')->with('success', 'Email Template Status Successfully Updated!');
	}
}
