<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Socialmedia;

class SocialmediaController extends Controller
{
    public function index()
    {
        $socialmedias = Socialmedia::all();
		return view('admin/socialmedia.index', compact('socialmedias'));
    }   
    public function store(Request $request)
    {
        		
		for($i = 1; $i <= 6; $i++) {
			$status = $displayorder = 0;
			$sociallink = '';
			$statusfield = 'status_'.$i;
			$linkfield = 'sociallink_'.$i;
			$orderfield = 'display_order_'.$i;
			if(isset($request->{$statusfield})) {
				$status = 1;
			}
			if(isset($request->{$linkfield})) {
				$sociallink = $request->{$linkfield};
			}
			if(isset($request->{$orderfield})) {
				$displayorder = $request->{$orderfield};
			}
			
			Socialmedia::where('id', '=', $i)->update(array('status' => $status, 'sociallink' => $sociallink, 'display_order' => $displayorder));
		}		
		
		return redirect('/admin/socialmedia')->with('success', 'Social Link Successfully Updated!');
    }
}