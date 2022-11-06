<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::all();
        return view('admin.menu.index', compact('menu'));        
    }
    public function menu()
    {
        $menu = Menu::all();
        return view('admin/menu/index', compact('menu'));        
    }
    public function create()
    {
		$menu = Menu::all();		
        return view('admin.menu.add', compact('menu'));
    }
    public function store(Request $request)
    {
        $menu = new Menu;
		$menu->menuname = $request->name;
		$menu->menu_url = $request->menu_url;		
		$menu->parent_id = $request->parent_id;
		$menu->page_id = $request->page_id;	
		$menu->display_order = $request->display_order;			
		$menu->status = $request->status;
        $menu->save();	        
		return redirect('/admin/menu')->with('success', 'Menu Successfully Created!');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $menu = Menu::find($id);
        $menus = Menu::all();			
		return view('admin.menu.edit', compact('menu','menus'));
    }
    public function update(Request $request, Menu $menu)
    {
        
        $id = $request->id;
        $menu = Menu::find($id);
        Menu::where('id', '=', $id)->update(array('menuname' => $request->name, 
                                                  'parent_id' => $request->parent_id, 
                                                  'page_id' => $request->page_id, 
                                                  'menu_url' => $request->menu_url,
                                                  'display_order' =>  $request->display_order,
                                                  'status' => $request->status));  
			
		return redirect('/admin/menu')->with('success', 'Menu Successfully Updated!');
    }
    public function destroy($id)
    {
        Menu::where('id', '=', $id)->delete();
		return redirect('/admin/menu')->with('success', 'Menu Successfully Deleted!');
    }
	
	public function updatestatus($id, $status) {
       	$statusval = 1;
		if($status == 1) {
			$statusval = 0;
		}
		Menu::where('id', '=', $id)->update(array('status' => $statusval));		
		return redirect('/admin/menu')->with('success', 'Menu Status Successfully Updated!');
	}
	public function bulkupdate(Request $request) {
		/*print_r($request->all());
		die();*/
		$bulkaction = $request->bulk_action;
		$menuid = $request->menuid;
		if($bulkaction == 'delete') {
			if(is_array($menuid)) {
				foreach($menuid as $menuid) {					
					Menu::where('id', '=', $menuid)->delete();
				}
			}			
		} 
		else
		{
			if(is_array($menuid)) {				
				/*print_r($menuid);
				die();	*/			
				foreach($menuid as $menuid) 
				{	
					$field = 'display_order'.$menuid;
					$displayorder = $request->{$field};					
					Menu::where('Id', '=', $menuid)->update(array('display_order' => $displayorder));
				}				
			}
		}
		return redirect('/admin/menu')->with('success', 'Menu Successfully Updated!');
	}
}