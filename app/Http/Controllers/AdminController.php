<?php

namespace App\Http\Controllers;

use App\Models\ManageAdmin;
use App\Models\RoleRights;
use Hash;
use Illuminate\Http\Request;
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

        $username = $request->username;
        $admin = ManageAdmin::where('username', '=', $username)->first();
        if ($admin) {
            $password = Hash::check($request->password, $admin->password);
            if ($password) {
                Session::put('admin_id', $admin->id);
                Session::put('priority', $admin->priority);

                if ($admin->priority > 0) {
                    $rolerights = RoleRights::where('id', '=', $admin->priority)->first();
                    if ($rolerights) {
                        $accessrights = @explode(',', $rolerights->rights);
                        Session::put('accessrights', $accessrights);
                    }
                }
                return redirect('/admin/dashboard');
            } else {
                return redirect()->back()->withInput($request->only('username', 'remember'))->with('message', 'Invalid Login Credentials!');
            }
        } else {
            return redirect()->back()->withInput($request->only('username', 'remember'))->with('message', 'Invalid Login Credentials!');
        }

    }

    public function logout()
    {
        Session::forget('admin_id');
        Session::forget('priority');
        Session::forget('ninja_authkey');
        return redirect('/admin/');
    }
}
