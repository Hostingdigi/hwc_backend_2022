<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SMS;


class SMSTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {		
        $smstemplate = SMS::all();
		return view('admin/SMSTemplate.index', compact('smstemplate'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {		
        return view('admin/SMSTemplate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {		
        $smstemplate = new SMS;
        $smstemplate->templatename = $request->templatename;	
        $smstemplate->subject = $request->subject;	
        $smstemplate->content = $request->content;		
		$smstemplate->status = $request->status;
		$smstemplate->template_type = $request->template_type;
		$smstemplate->save();		
		return redirect('/admin/smstemplate')->with('success', 'SMS Template Successfully Created!');
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
        $smstemplate = SMS::where('id', '=', $id)->first();			
		return view('admin/SMSTemplate.edit', compact('smstemplate'));
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
		
		SMS::where('id', '=', $id)->update(array('templatename' => $request->templatename,'subject' => $request->subject, 'content' => $request->content,  'status' => $request->status, 'template_type' => $request->template_type));
		
		return redirect('/admin/smstemplate')->with('success', 'SMS Template Successfully Updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        SMS::where('id', '=', $id)->delete();
		return redirect('/admin/smstemplate')->with('success', 'SMS Template Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		SMS::where('id', '=', $id)->update(array('status' => $statusval));
		
		return redirect('/admin/smstemplate')->with('success', 'SMS Template Status Successfully Updated!');
	}
}
