<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\MastheadImage;
use Session;

class MastheadImageController extends Controller
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
        $mastheadimages = MastheadImage::paginate(25);
        return view('admin.mastheadimage.index', compact('mastheadimages', 'moduleaccess', 'adminrole'));
    }
    public function create()
    {
        return view('admin.mastheadimage.create');
    }
    public function store(Request $request)
    {
        $mastheadimage = new MastheadImage;
		$mastheadimage->ban_name = $request->ban_name;
        $mastheadimage->ban_link = $request->ban_link;
		$filename = $mobilefilename = "";		        
        if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/bannermaster');
			$image->move($destinationPath, $filename);                                
		}
		if($request->hasFile('EnBanMobileimage')) {
			$image = $request->file('EnBanMobileimage');			
			$mobilefilename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/bannermaster');
			$image->move($destinationPath, $mobilefilename);                                
		}
		
		
		$mastheadimage->EnBanimage = $filename;
		$mastheadimage->EnBanMobileimage = $mobilefilename;
        $mastheadimage->ban_caption = $request->ban_caption;
        $mastheadimage->ban_status = $request->ban_status;
        $mastheadimage->save();	        
		return redirect('/admin/banner_master')->with('success', 'Banner Successfully Created!');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $mastheadimage = MastheadImage::where('ban_id', '=', $id)->first();		
		
        return view('admin.mastheadimage.edit', compact('mastheadimage'));      
    }
    public function update(Request $request)
    {
        $id = $request->id;
		
		$filename = $request->ExistEnBanimage;
		$mobilefilename = $request->ExistEnBanMobileimage;
		
		
        if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/bannermaster');
			$image->move($destinationPath, $filename);                                
		}
		
		if($request->hasFile('EnBanMobileimage')) {
			$image = $request->file('EnBanMobileimage');			
			$mobilefilename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/bannermaster');
			$image->move($destinationPath, $mobilefilename);                                
		}
		

		MastheadImage::Where('ban_id', '=', $id)->update(array('ban_name' => $request->ban_name, 'ban_link' => $request->ban_link, 'EnBanimage' => $filename, 'EnBanMobileimage' => $mobilefilename, 'ban_caption' => $request->ban_caption, 'ban_status' => $request->ban_status));
	   	
		return redirect('/admin/banner_master')->with('success', 'Banner Successfully Updated!');
    }
    public function destroy($id)
    {
        MastheadImage::where('ban_id', '=', $id)->delete();
		return redirect('/admin/banner_master')->with('success', 'Banner Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		MastheadImage::where('ban_id', '=', $id)->update(array('ban_status' => $statusval));		
		return redirect('/admin/banner_master')->with('success', 'Banner Status Successfully Updated!');
	}	
	public function bulkupdate(Request $request) {
		/*print_r($request->all());
		die();*/
		$bulkaction = $request->bulk_action;
		$mastheadimageid = $request->mastheadimageid;
		if($bulkaction == 'delete') {
			if(is_array($mastheadimageid)) {
				foreach($mastheadimageid as $mastheadimageid) {					
					MastheadImage::where('ban_id', '=', $mastheadimageid)->delete();
				}
			}			
		} 
		else
		{
			if(is_array($mastheadimageid)) {				
				/*print_r($menuid);
				die();	*/			
				foreach($mastheadimageid as $mastheadimageid) 
				{	
					$field = 'display_order'.$mastheadimageid;
					$displayorder = $request->{$field};					
					MastheadImage::where('ban_id', '=', $mastheadimageid)->update(array('display_order' => $displayorder));
				}				
			}
		}
		return redirect('/admin/banner_master')->with('success', 'Banner Successfully Updated!');		
    
}
    
}