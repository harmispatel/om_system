<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\facades\Session;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin')->except('admin.logout');
    }

    // Show Admin Login Form
    public function showAdminLogin(Request $request)
    {
        session(['link' => url()->previous()]);
        return view('auth.admin.login');
    }



    // Authenticate the Admin User
    public function Adminlogin(Request $request)
    {
        $input = $request->except('_token');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::guard('admin')->attempt($input)) {
            $status = Auth::guard('admin')->user()->status;
            if($status == 1){
                $username = Auth::guard('admin')->user()->firstname." ".Auth::guard('admin')->user()->lastname;
                $intendedUrl = $request->session()->pull('url.intended', route('admin.dashboard'));
                return redirect()->to($intendedUrl)->with('success', 'Welcome '.$username);
            }else{
                Auth::guard('admin')->logout();
                return redirect()->route('admin.login')->with('error', 'Your Account has been Temporarily Blocked!');
            }
        }
        return back()->with('error', 'Please Enter Valid Email & Password');
    }
}
