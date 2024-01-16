<?php

namespace App\Http\Controllers;
use App\Models\Reason;
use App\Models\Permission;
use App\Models\types_work;
use Illuminate\Http\Request;
use App\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class ReasonController extends Controller
{
   public function index(Request $request){

    if($request->ajax()){

        if($request->departments != 0){
            $reasons = Reason::with('departments')->where('department',$request->departments)->get();
        }else{
            $reasons = Reason::with('departments')->get();
        }
        return DataTables::of($reasons)
        ->addColumn('department',function($row){

          $getDepartmentName = isset($row['departments']->name)? $row['departments']->name:'';

          return $getDepartmentName;
        })
        ->rawColumns(['department'])
        ->make(true);
    }
     $departments = Role::get();
     return view('admin.Reason.index',compact('departments'));
   }
   public function create(){

        $departments = Role::get();
        return view('admin.Reason.create',compact('departments'));
   }

   public function store(Request $request){

       $request->validate([
         'department' => 'required',
          'reason'  => 'required'
       ]);

       $getNameOfDep = Role::where('id',$request->department)->first();

    try{

        $input = $request->except('_token');
        $storeReason = Reason::create($input);

        return redirect()->route('reasons.create')->with('success','Added SuccessFully Reason For '.$getNameOfDep->name);
     }catch(\Throwable $th){
        return redirect()->route('reasons.create')->with('error','Internal Server Error!');
     }
   }
}
