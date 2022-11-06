<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PageContent;
use Session;

class PageContentController extends Controller
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
		
        $pagecontents = PageContent::orderBy('EnTitle', 'ASC')->paginate(20);      
        return view('admin.pagecontent.index', compact('pagecontents', 'moduleaccess', 'adminrole'));        
    }
    public function create()
    {	
		$pagecontent = PageContent::all(); 
			
        return view('admin.pagecontent.create', compact('pagecontent'));
    }
    public function store(Request $request)
    {		
        $pagecontent = new PageContent;
		$pagecontent->EnTitle = $request->EnTitle;
		$pagecontent->EnContent =$request->ShortDesc;
		$pagecontent->parent_id = $request->parent_id;		
        $pagecontent->UniqueKey = $request->UniqueKey;
        $pagecontent->page_link = $request->UniqueKey;
        $pagecontent->menu_type = $request->menu_type;
        $pagecontent->banner_type = $request->banner_type;
		$filename = "";
        if($request->hasFile('banner_image')) {
			$image = $request->file('banner_image');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/pagecontent');
			$image->move($destinationPath, $filename);                                
		}
        $pagecontent->banner_image = $filename;
        $pagecontent->ChContent = $request->ChContent;
		$pagecontent->meta_title = $request->meta_title;
        $pagecontent->meta_keywords = $request->meta_keywords;
        $pagecontent->meta_description = $request->meta_description;
        $pagecontent->rights_to_visible = $request->rights_to_visible;
        $pagecontent->display_status = $request->display_status;
		$pagecontent->save();		
		return redirect('/admin/static_pages')->with('success', ' Page Content Successfully Created!');
    }
    public function edit($id)
    {
        $pagecontent = PageContent::where('Id', '=', $id)->first();	
		$pagecont = PageContent::all(); 		
		return view('admin/pagecontent.edit', compact('pagecontent','pagecont'));
    }

    public function update(Request $request)
    {
        $id = $request->id;		
		$filename = $request->exist_banner_image;
        if($request->hasFile('banner_image')) {
			$image = $request->file('banner_image');			
			$filename = time().'_'.$image->getClientOriginalName();
			$destinationPath = public_path('uploads/pagecontent');
			$image->move($destinationPath, $filename);                                
		}
		PageContent::where('Id', '=', $id)->update(array('EnTitle' => $request->EnTitle, 'EnContent' => $request->ShortDesc, 'parent_id' => $request->parent_id, 'UniqueKey' => $request->UniqueKey, 'page_link' => $request->UniqueKey, 'menu_type' => $request->menu_type, 'banner_type' => $request->banner_type, 'banner_image' => $filename, 'ChContent' => $request->ChContent, 'meta_title' => $request->meta_title, 'meta_keywords' => $request->meta_keywords, 'meta_description' => $request->meta_description, 'rights_to_visible' => $request->rights_to_visible, 'display_status' => $request->display_status));
		
		return redirect('/admin/static_pages')->with('success', 'Page Content Successfully Updated!');
    }


    public function destroy($id)
    {
        PageContent::where('Id', '=', $id)->delete();
		return redirect('/admin/static_pages')->with('success', 'Page Content Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
		$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		PageContent::where('Id', '=', $id)->update(array('display_status' => $statusval));
		
		return redirect('/admin/static_pages')->with('success', 'Page Content Status Successfully Updated!');
	}
	
	public function displayorder($id, $display_order)
	{
		$id = $request->input('id');
		$display_order = $request->input('display_order');
		PageContent::where('Id', '=', $id)->update(array('display_order' => $display_order));
		return response()->json("Success");
	}
	public function bulkupdate(Request $request) {
		/*print_r($request->all());
		die();*/
		$bulkaction = $request->bulk_action;
		$pagecontentids = $request->pagecontentids;
		if($bulkaction == 'delete') {
			if(is_array($pagecontentids)) {
				foreach($pagecontentids as $pagecontentids) {					
					PageContent::where('Id', '=', $pagecontentids)->delete();
				}
			}	
		
		} 
		else
		{
			if(is_array($pagecontentids)) {
				
				/*print_r($pagecontentids);
				die();*/
				
				foreach($pagecontentids as $pagecontentids) 
				{	
					$field = 'display_order'.$pagecontentids;
					$displayorder = $request->{$field};					
					PageContent::where('Id', '=', $pagecontentids)->update(array('display_order' => $displayorder));
				}
				
			}
		}
		return redirect('/admin/static_pages')->with('success', 'Page Content Successfully Updated!');
	}
}
