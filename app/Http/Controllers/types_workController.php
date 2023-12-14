<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\types_work;
use DataTables;

class types_workController extends Controller
{
    public function index(Request $request){

            if($request->ajax()){
                    
                    $orders = types_work::get();
                    return DataTables::of($orders)
                    ->addIndexColumn() 
                    ->editColumn('works_time', function ($time) {
                        return $time->working_hours.' : '.$time->working_minutes.' : '.$time->working_seconds;
                         }) 
                    ->addColumn('actions',function($row){
                        $role_id = isset($row->id) ? encrypt($row->id) : '';
                      $action_html = '<a href='.route("edit.types_work",["id" => $role_id]).' class="btn btn-sm btn-info rounded-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';  
                      return $action_html;
                    })
                    ->rawColumns(['actions'])
                    ->make(true);  
            }
        return view('admin.Types_of_Work.types_of_work');
    }

    public function edit(Request $request,$id){

        $id = decrypt($id);
      
        $role = types_work::find($id);
    
        return view('admin.Types_of_Work.edit_types_of_work',compact('role'));
    }

    public function update(Request $request){
      
        $this->validate($request, [
            'working_hours'=>'required',
            'working_minutes' => 'required'
        ]);

        $id = decrypt($request->id);

        $types = types_work::find($id);
        //  $types->types_of_works = $request->types_of_works;
        //  $types->order_value = $request->order_value;
        $types->working_hours = $request->working_hours;
        $types->working_minutes = $request->working_minutes;
        $types->working_seconds = $request->working_seconds;
        $types->update();

        return redirect()->route('types_work')->with('success' , 'Types works updated successfull');
    }
}
