<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManageAdmin;
use App\Models\PasswordChange;
use Hash;
use Illuminate\Http\Request;
use Session;

class PasswordChangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $passwordchange = PasswordChange::all();
        $adminid = 0;
        if (Session::has('admin_id')) {
            $adminid = Session::get('admin_id');
        }

        return view('admin.passwordchange.index', compact('passwordchange', 'adminid'));
    }

    public function store(Request $request)
    {
        $existpassword = '';
        $admin = ManageAdmin::where('id', '=', $request->adminid)->first();

        if ($admin) {
            //if (!(Hash::check($request->get('oldpassword'), PasswordChange::admin()->password))) {
            if (!(Hash::check($request->get('oldpassword'), $admin->password))) {
                // The passwords matches
                return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
            }

            if (strcmp($request->get('oldpassword'), $request->get('newpassword')) == 0) {
                //Current password and new password are same
                return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
            }

            /*$validatedData = $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
            ]);*/

            //Change Password
            /*$user = ManageAdmin::find($adminid);
            $user->password = Hash::make($request->get('newpassword'));
            $user->save();*/

            ManageAdmin::where('id', '=', $request->adminid)->update(array('password' => Hash::make($request->get('newpassword'))));

            return redirect()->back()->with("success", "Password changed successfully !");
        }

    }

}
