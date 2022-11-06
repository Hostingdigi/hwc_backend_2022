<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Hash;
use App\Models\PasswordChange;


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
		return view('admin.passwordchange.index', compact('passwordchange'));
    }   
    
    public function store(Request $request){

        if (!(Hash::check($request->get('oldpassword'), PasswordChange::user()->password))) {
            // The passwords matches
            return redirect()->back()->with("error","Your current password does not matches with the password you provided. Please try again.");
        }

        if(strcmp($request->get('oldpassword'), $request->get('newpassword')) == 0){
            //Current password and new password are same
            return redirect()->back()->with("error","New Password cannot be same as your current password. Please choose a different password.");
        }

        $validatedData = $request->validate([
            'oldpassword' => 'required',
            'newpassword' => 'required|string|min:6|confirmed',
        ]);

        //Change Password
        $user = PasswordChange::user();
        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        return redirect()->back()->with("success","Password changed successfully !");

    }
    
}
