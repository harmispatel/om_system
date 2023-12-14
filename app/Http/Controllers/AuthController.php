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
        $userdetail = Admin::where('email',$request->email)->first();
        $userStatus = (isset($userdetail->status)) ? $userdetail->status : '';
        
        $input = $request->except('_token');

        $request->validate([    
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($userStatus == 1)
        {
              if(Auth::guard('admin')->attempt($input))
              {
       
                $username = Auth::guard('admin')->user()->firstname." ".Auth::guard('admin')->user()->lastname;
                return redirect()->route('admin.dashboard')->with('success', 'Welcome '.$username);
                // return redirect()->intended();
             
              }
        }

         return back()->with('error', 'Please Enter Valid Email & Password');
    }
}