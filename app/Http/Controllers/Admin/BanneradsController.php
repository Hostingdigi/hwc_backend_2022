<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bannerads;
use App\Models\PageContent;
use Input;
use Session;


class BanneradsController extends Controller
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
        $bannerads = Bannerads::paginate(25);
		
		return view('admin.bannerads.index', compact('bannerads', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$bannerads = Bannerads::all();	
		$pages = PageContent::orderBy('EnTitle', 'ASC')->get();		
        return view('admin.bannerads.create', compact('bannerads', 'pages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		
        $bannerads = new Bannerads;
		$bannerads->ban_name = $request->ban_name;
        $bannerads->ban_link = $request->ban_link;	
        if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/bannerads');
			$image->move($destinationPath, $filename); 
			$bannerads->EnBanimage = $filename;			
        }	
		
		$videofile = '';
		
		$videofile = $request->Video;
		
		$bannerads->Video = $videofile;
		$bannerads->ban_caption = $request->ban_caption;
		$bannerads->ban_status = $request->ban_status;
		$bannerads->PageId = $request->PageId;
		$bannerads->display_order = $request->display_order;
		$bannerads->save();		
		return redirect('/admin/banner_ads')->with('success', 'Banner Ads Successfully Created!');
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
        $bannerads = Bannerads::where('ban_id', '=', $id)->first();
		$pages = PageContent::orderBy('EnTitle', 'ASC')->get();			
		return view('admin.bannerads.edit', compact('bannerads', 'pages'));
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
		$filename = $request->ExistEnBanimage;        
		if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');						
		$filename = time().'_'.$image->getClientOriginalName();			
		$destinationPath = public_path('uploads/bannerads');			
		$image->move($destinationPath, $filename);                                		
		}	
		
		
		$videofile = $request->Video;
		
		
		Bannerads::Where('ban_id', '=', $id)->update(array('ban_name' => $request->ban_name, 'ban_link' => $request->ban_link, 'EnBanimage' => $filename, 'ban_caption'=> $request->ban_caption,'display_order' => $request->display_order, 'ban_status' => $request->ban_status, 'Video' => $videofile, 'PageId' => $request->PageId));	
		return redirect('/admin/banner_ads')->with('success', 'Banner Ads Successfully Updated!');
    }

    public function destroy($id)
    {
        Bannerads::where('ban_id', '=', $id)->delete();
		return redirect('/admin/banner_ads')->with('success', 'Banner Ads Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Bannerads::where('ban_id', '=', $id)->update(array('ban_status' => $statusval));		
		return redirect('/admin/banner_ads')->with('success', 'Banner Ads Status Successfully Updated!');
	}	
	public function bulkupdate(Request $request) {
		/*print_r($request->all());
		die();*/
		$bulkaction = $request->bulk_action;
		$banneradsid = $request->banneradsid;
		if($bulkaction == 'delete') {
			if(is_array($banneradsid)) {
				foreach($banneradsid as $banneradsid) {					
					Bannerads::where('ban_id', '=', $banneradsid)->delete();
				}
			}			
		} 
		else
		{
			if(is_array($banneradsid)) {				
				/*print_r($menuid);
				die();	*/			
				foreach($banneradsid as $banneradsid) 
				{	
					$field = 'display_order'.$banneradsid;
					$displayorder = $request->{$field};					
					Bannerads::where('ban_id', '=', $banneradsid)->update(array('display_order' => $displayorder));
				}				
			}
		}
		return redirect('/admin/banner_ads')->with('success', 'Banner Ads Successfully Updated!');
	}
}
