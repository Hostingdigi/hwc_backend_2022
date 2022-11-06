<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
Use App\Models\Settings;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::where('id', '=', '1')->first();
		return view('admin/settings.index', compact('settings'));
    }   
    public function update(Request $request)
    {
       
		$id = $request->id;
		
		Settings::where('id', '=', $id)->update(array('site_path' => $request->site_path, 'download_path' => $request->download_path, 'company_name' => $request->company_name, 'name' => $request->company_name, 'company_address' => $request->company_address, 'company_phone' => $request->company_phone, 'company_fax' => $request->company_fax, 'GST_res_no' => $request->GST_res_no, 'admin_email' => $request->admin_email, 'enquiries_email' => $request->enquiries_email, 'cc_email' => $request->cc_email, 'bcc_email' => $request->bcc_email, 'date_format' => $request->date_format, 'site_title' => $request->site_title, 'database_server' => $request->database_server, 'database_username' => $request->database_username, 'database_password' => $request->database_password, 'database_name' => $request->database_name, 'notify_min_prod_qty' => $request->notify_min_prod_qty, 'img_files_allowed_ext' => $request->img_files_allowed_ext, 'files_allowed_ext' => $request->files_allowed_ext, 'files_allowed_size' => $request->files_allowed_size, 'files_allowed_h' => $request->files_allowed_h, 'files_allowed_w' => $request->files_allowed_w, 'price1' => $request->price1, 'price2' => $request->price2, 'price3' => $request->price3, 'price4' => $request->price4, 'noofnews' => $request->noofnews, 'prod_weight' => $request->prod_weight));
		
		return redirect('/admin/subadmin_settings')->with('success', 'Settings Successfully Updated!');
    }
}