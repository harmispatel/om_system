<?php

namespace App\Http\Controllers;

use App\Models\{Admin, User,order};
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user_type = Auth::guard('admin')->user()->user_type;
         
        if($user_type == 1){
            $ordersCount = order::all()->count();
        }else{
            $ordersCount= order::where('order_status',$user_type)->count();
        }
        $userCount = Admin::all()->count();
        return view('admin.dashboard',compact('userCount','ordersCount'));
    }
}
