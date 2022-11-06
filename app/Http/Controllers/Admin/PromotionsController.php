<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotions;
use Session;

class PromotionsController extends Controller
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
        $promotions = Promotions::paginate(25);
		return view('admin.promotions.index', compact('promotions', 'moduleaccess', 'adminrole'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		$promotions = Promotions::all();		
        return view('admin.promotions.create', compact('promotions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $promotions = new Promotions;
		$promotions->ban_name = $request->ban_name;
        $promotions->ban_link = $request->ban_link;	
		$filename = $mobilefilename = '';
        if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/promotionsbanner');
			$image->move($destinationPath, $filename);                                
		}	
		if($request->hasFile('EnBanMobileimage')) {
			$image = $request->file('EnBanMobileimage');			
			$mobilefilename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/promotionsbanner');
			$image->move($destinationPath, $mobilefilename);                                
		}	
		$promotions->EnBanimage = $filename;
		$promotions->EnBanMobileimage = $mobilefilename;
		$promotions->ban_caption = $request->ban_caption;		
		$promotions->ban_status = $request->ban_status;
		$promotions->save();		
		return redirect('/admin/promotions')->with('success', 'Promotions Successfully Created!');
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
        $promotions = Promotions::where('ban_id', '=', $id)->first();			
		return view('admin.promotions.edit', compact('promotions'));
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
		$mobilefilename = $request->ExistEnBanMobileimage;        
		if($request->hasFile('EnBanimage')) {
			$image = $request->file('EnBanimage');
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/promotionsbanner');
			$image->move($destinationPath, $filename);
		}
		if($request->hasFile('EnBanMobileimage')) {
			$image = $request->file('EnBanMobileimage');
			$mobilefilename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/promotionsbanner');
			$image->move($destinationPath, $mobilefilename);
		}
			
		Promotions::Where('ban_id', '=', $id)->update(array('ban_name' => $request->ban_name, 'ban_link' => $request->ban_link, 'EnBanimage' => $filename, 'EnBanMobileimage' => $mobilefilename, 'ban_caption' => $request->ban_caption, 'ban_status' => $request->ban_status));		
		return redirect('/admin/promotions')->with('success', 'Promations Successfully Updated!');
    }

    public function destroy($id)
    {
        Promotions::where('ban_id', '=', $id)->delete();
		return redirect('/admin/promotions')->with('success', 'Promotions Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Promotions::where('ban_id', '=', $id)->update(array('ban_status' => $statusval));		
		return redirect('/admin/promotions')->with('success', 'Promotions Status Successfully Updated!');
	}	
	public function bulkupdate(Request $request) {
		/*print_r($request->all());
		die();*/
		$bulkaction = $request->bulk_action;
		$promotionsid = $request->promotionsid;
		if($bulkaction == 'delete') {
			if(is_array($promotionsid)) {
				foreach($promotionsid as $promotionsid) {					
					Promotions::where('ban_id', '=', $promotionsid)->delete();
				}
			}			
		} 
		else
		{
			if(is_array($promotionsid)) {				
				/*print_r($menuid);
				die();	*/			
				foreach($promotionsid as $promotionsid) 
				{	
					$field = 'display_order'.$promotionsid;
					$displayorder = $request->{$field};					
					Promotions::where('ban_id', '=', $promotionsid)->update(array('display_order' => $displayorder));
				}				
			}
		}
		return redirect('/admin/promotions')->with('success', 'Promotions Successfully Updated!');
	}
}
