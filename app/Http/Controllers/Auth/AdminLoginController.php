<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Session;

class AdminLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin');
    }
    
    
    public function showLoginForm()
    {
        return view('admin_login');
    }
    public function login(Request $request)
    {
        /*$this->validate($request, [
            'username' => 'required|min:6',
            'password' => 'required|min:6'
        ]);*/
		
        if(Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password],$request->remember))
        {			
            return redirect()->route('admin.dashboard');
        }
        return redirect()->back()->withInput($request->only('username','remember'));
    }
}