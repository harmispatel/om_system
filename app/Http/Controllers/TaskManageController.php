<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use App\Models\Admin;
use App\Models\Task_manage;
use App\Models\types_work;
use Yajra\DataTables\DataTables;

use Illuminate\Http\Request;

class TaskManageController extends Controller
{
     public function index(){
        $types_of_works = types_work::get();
        $permission = Permission::get();
        $inSwitches = Permission::where('permission_type','1')->get();    
        $outSwitches = Permission::where('permission_type','2')->get();    
        return view('admin.taskmanage.taskmanage',compact('permission','types_of_works','inSwitches','outSwitches'));
    }

    public function create(Request $request){
     
        $this->validate($request,[
            'switch1'=>'required',
            'switch2'=>'required',
            "work_hours" =>'required',
            "work_minutes" => 'required',
            "work_seconds" => 'required',
            'type_of_work' => 'required',
        ]);
        try{
            $input = $request->except('_token');
            $new1 = $request->switch1;
            $new2 = $request->switch2;
           
            $task_manage = new Task_manage;
            $task_manage->task1_id  = $new1;
            $task_manage->task2_id  = $new2; 
            $task_manage->working_hours = $request->work_hours; 
            $task_manage->working_minutes = $request->work_minutes; 
            $task_manage->working_seconds = $request->work_seconds; 
            $task_manage->types_of_works = $request->type_of_work;  
            $task_manage->save();
        
            return redirect()->back()->with('success','Task Schedule added SuccessFully');
        }catch(\Throwable $th){
          return redirect()->route('task-management')->with('error','internel server Error !');
        }
    }


    public function list(Request $request){
     
      if($request->ajax()){
       $getTaskSchedule = Task_manage::get();
       return DataTables::of($getTaskSchedule)
       ->addIndexColumn()
       ->editColumn('works_time', function ($time) {
        return $time->working_hours.' : '.$time->working_minutes.' : '.$time->working_seconds;
         }) 
       ->editColumn('types_of_works',function($getTaskSchedule){
            $taskid = $getTaskSchedule->types_of_works;
       
            $types_of_works = types_work::where('id',$taskid)->first();

            return isset($types_of_works) ? $types_of_works->types_of_works :'';
       })
       ->editColumn('task1_id',function($getTaskSchedule){
         $taskId = $getTaskSchedule->task1_id;
         $getPermisionRecord = Permission::where('id',$taskId)->first();
         return $getPermisionRecord->name;

       })
       ->editColumn('task2_id',function($getTaskSchedule){
        $taskId = $getTaskSchedule->task2_id;
        $getPermisionRecord = Permission::where('id',$taskId)->first();
        return $getPermisionRecord->name;
       })
       ->addColumn('actions',function($row){
        $role_id = isset($row->id) ? encrypt($row->id) : '';
        $action_html = '<a href='.route("task-manage.edit",["id" => $role_id]).' class="btn btn-sm btn-info rounded-circle"><i class="fa fa-pencil" aria-hidden="true"></i></a>';  
        $action_html .= '<a  onclick="deleteOrderRecord(\''.$row->id.'\')" class="btn btn-sm btn-danger rounded-circle"><i class="fa fa-trash" aria-hidden="true"></i></a>';
       return $action_html;
      })
      ->rawColumns(['actions'])
       ->make(true);
      }
      return view('admin.taskmanage.task-manage-list');
    }
    
    public function edit(Request $request,$id){

      $id = decrypt($id);
      $taskManage = Task_manage::find($id);

      $TypesOf_Works = types_work::get();
      $permission = Permission::get();    

      return view('admin.taskmanage.edit-task-manage',compact('taskManage','TypesOf_Works','permission'));
    }

    public function update(Request $request){
    
      try{

        $id = decrypt($request->id);

        $taskmanage = Task_manage::find($id);
        $taskmanage->working_hours = $request->working_hours;
        $taskmanage->working_minutes = $request->working_minutes;
        $taskmanage->working_seconds = $request->working_seconds;
        $taskmanage->update();

        return redirect()->back()->with('success','Task Schedule updated SuccessFully');

      }catch(\Throwable $th){
        return redirect()->route('task-management')->with('error','internel server Error !');
      }
      
      

    }
    
    public function destroy(Request $request)
    {
        try
        {
            $order = Task_manage::where('id',$request->id)->delete();

            return response()->json(
            [
                'success' => 1,
                'message' => "Order deleted Successfully..",
            ]);
        }
        catch (\Throwable $th)
        {
            return response()->json(
            [
                'success' => 0,
                'message' => "Something with wrong",
            ]);
        }
    }
}
