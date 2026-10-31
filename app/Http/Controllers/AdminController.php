<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class AdminController extends Controller
{

    /*public function __construct()
    {
    $this->middleware('auth:admin');
    }*/

    public function login()
    {

        return view("admin_login");
    }

    public function index()
    {
        return view("admin_login");
        //return view("admin.dashboard_main");
    }

    public function dashboard()
    {
        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }

        return view("admin.dashboard_main", compact('moduleaccess', 'adminrole'));
    }

    public function verifylogin(Request $request)
    {
        $admin = Admin::where('username', '=', (string) $request->username)->first();

        if (!$admin || !Hash::check((string) $request->password, $admin->password)) {
            return redirect()->back()->withInput($request->only('username', 'remember'))->with('message', 'Invalid Login Credentials!');
        }

        // Logs in on the "admin" guard and regenerates the session id (no session fixation).
        Auth::guard('admin')->login($admin);

        // Legacy session keys still read by the admin views and controllers
        Session::put('admin_id', $admin->id);
        Session::put('priority', $admin->priority);
        Session::forget('accessrights');
        if (!$admin->isSuperAdmin()) {
            Session::put('accessrights', $admin->moduleRights());
        }

        return redirect('/admin/dashboard');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        Session::forget(['admin_id', 'priority', 'accessrights', 'ninja_authkey']);
        return redirect('/admin/');
    }
}
