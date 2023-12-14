<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\order;
use App\Models\Order_history;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Task_manage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use PhpParser\Node\Stmt\Return_;

class ReportController extends Controller
{

    public function orderHistoryDetail(Request $request,$id){

      if($request->ajax()){
        
        $order_history = Order_history::where('order_id',$request->id)->with('receivePermission','issuePermission')->get();
        
        return DataTables::of($order_history)
        ->addIndexColumn()
        
        ->addColumn('order_no',function($row){
           $getOrderDetails = order::where('id',$row->order_id)->first();
           return isset($getOrderDetails->orderno)?$getOrderDetails->orderno:'' ;
        })
        ->addColumn('inswitch_time',function($row){
          $getReceiveDate = isset($row->receive_time)? $row->receive_time:'';
          if($getReceiveDate == null){
            $receiveTime = 'Not Received Order...';
          }else{
            $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time)) ;
          }
         
          $PermissionName = isset($row->receivePermission)? $row->receivePermission->name :'';
          $outswitch_html = '<div class="row" style="font-size:20px;">';
          $outswitch_html .=  '<span>'.$PermissionName.'</span>';
          $outswitch_html .= '</div>';
          $outswitch_html .= '<div class="row" style="font-size:14px;">';
          $outswitch_html .=  ' <span>'.$receiveTime.'</span>';
          $outswitch_html .= '</div>';
          return $outswitch_html;
        })
        ->addColumn('outswitch_time',function($row){
          $getIssueDate = isset($row->issue_time)? $row->issue_time:'';
          if($getIssueDate == null){
            $issueTime = 'In Working...';
          }else{
            $issueTime =  date('d-m-Y h:i:s', strtotime($getIssueDate)); 
          }
          $PermissionName = isset($row->issuePermission)? $row->issuePermission->name:'';
          $outswitch_html = '<div class="row" style="font-size:20px;">';
          $outswitch_html .=  '<span>'.$PermissionName.'</span>';
          $outswitch_html .= '</div>';
          $outswitch_html .= '<div class="row" style="font-size:14px;">';
          $outswitch_html .=  ' <span>'.$issueTime.'</span>';
          $outswitch_html .= '</div>';
          return $outswitch_html;
        })
        ->addColumn('user_name',function($row){
          $getUserName = Admin::where('id',$row->user_id)->first();
          $userName = ($getUserName->firstname) .' '. ($getUserName->lastname);
          return $userName;
        })
        ->addColumn('duration',function($row){
           
            $taskTime = Task_manage::where('types_of_works',$row->typesofwork_id)
                                    ->where('task1_id',$row->receive_switch)
                                    ->where('task2_id', $row->switch_type)
                                    ->first();
                
                
            $taskHours = isset($taskTime->working_hours) ? (int)$taskTime->working_hours : 0;
            $taskMinutes = isset($taskTime->working_minutes) ? (int)$taskTime->working_minutes : 0;
            $taskSeconds = isset($taskTime->working_seconds) ? (int)$taskTime->working_seconds : 0;

            $time = Carbon::createFromTime($taskHours, $taskMinutes, $taskSeconds);
            $formattedTime = $time->format('H:i:s');

             
            $startDateTime = Carbon::parse($row->receive_time) ;
            $endDateTime =Carbon::parse($row->issue_time) ;
            
            $difference = $endDateTime->diff($startDateTime)->format('%H:%i:%s');
            $formattedTimeInSeconds = strtotime($formattedTime);
            $differenceInSeconds = strtotime($difference);
            
             if ($differenceInSeconds >= $formattedTimeInSeconds) {
              // If true, return the difference time in red font
              $diff = '<span style="color: red;">' .$endDateTime->diff($startDateTime)->format('%d days %h hours %i minute'). '</span>';
              } else {
                  // If false, return the difference time in green font
                  $diff = '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute'). '</span>';
              }
            return $diff;
           
        })
        ->rawColumns([
           'index',
            'order_no',
            'inswitch_time',
            'outswitch_time',
            'user_name',
            'duration',
          ])
        ->make(true); 
      }
      return view('admin.reports.OrderHistory',compact('id'));
    }
   public function orderHistoryReport(Request $request){
  
     
    if($request->ajax()){
      $orders = order::get();
      $getRecord='';
      if (request()->has('order_number')) {
          
          if($request->order_number == null){
             $getRecord = order::latest()->take(10)->get();
          }else{
              $getRecord = order::where('orderno', 'LIKE', '%' . request('order_number') . '%')
            ->orWhere('orderno', 'LIKE', '%' . request('order_number') . '%')
            ->get(); 
          }
       }
      return DataTables::of($getRecord)
       ->addIndexColumn()
       ->addColumn('current_department',function($row){
          if($row->order_status == 11){
            return "<span class='badge bg-success '>Order Delivered</span>";
          }elseif($row->order_status == 12){
            return "<span class='badge bg-warning'>Saleing</span>";
          }else{
            $getDepartment = Role::where('id',$row->order_status)->first();
            return isset($getDepartment)? $getDepartment->name:'';
         
          }
        })
       ->addColumn('duration',function($row){

           if($row->order_status == 11){

              $getPermission = Permission::where('name','delivery/complete')->first();
              $order_history = Order_history::where('order_id',$row->id)->get();
              foreach($order_history as $value){
                //check in orderhistory records switch type = delivery
                 if($value->switch_type == $getPermission->id){
                  $order_create_date = Carbon::parse($row->created_at);
                  $delivery_date = Carbon::parse($row->deliverydate);
                  $actualDeliveryDate = Carbon::parse($value->issue_time);
  
                  // Check if actual delivery date is greater than or equal to defined delivery date
                  if ($actualDeliveryDate <= $delivery_date) {
                    return '<span style="color: green;">' . $actualDeliveryDate->diff($order_create_date)->format('%d days %h hours %i minute') . '</span>';
                      
                  } else {
                    return '<span style="color: red;">' . $actualDeliveryDate->diff($order_create_date)->format('%d days %h hours %i minute') . '</span>';
                  }
                 
                 }
              }
           }else{
             return '<span class="badge bg-warning">Pending..</span>';
           }
          return '';
        })
       
       ->addColumn('actions',function($row){
           $action_html = '<a href="'.route("reports.orderhistory.detail",$row->id).'" class="btn btn-sm btn-info rounded-circle"><i class="fa fa-eye" aria-hidden="true"></i></a>';  
            return $action_html;
        })
        ->rawColumns(['actions','current_department','duration'])
        ->make(true); 
    }
     return view('admin.reports.orderhistory-report');
   }
   public function typeOfWorkReport(){
     return view('admin.reports.typeofwork-report');
   }
   public function departmentPendingReport(Request $request){
     $departments= Role::get();

    if($request->ajax()){
     
      $startDate = $request->startDate;
      $endDate = $request->endDate;
      $order_history = Order_history::with('receivePermission','issuePermission')->get();
      
     
      if ($request->department == 0) {
        
        $records = Order_history::whereNotNull('receive_time')
                  ->whereNull('issue_time')
                  ->orWhere('issue_time', '')
                  ->whereNull('switch_type')
                  ->orWhere('switch_type', '')
                  ->with('receivePermission','issuePermission')->get(); 
                  if(isset($startDate) && isset($endDate)){
                    $records = $records->whereBetween('created_at', [$startDate, $endDate]);
                  }
                 
                 
        }else{
          $records = Order_history::whereNotNull('receive_time')
                    ->whereNull('issue_time')
                    ->orWhere('issue_time', '')
                    ->whereNull('switch_type')
                    ->orWhere('switch_type', '')
                    ->with('receivePermission','issuePermission')->get();  
          $records = $records->where('user_type', request('department'));
          if(isset($startDate) && isset($endDate)){
            $records = $records->whereBetween('created_at', [$startDate, $endDate]);
          }
        }
        
       
      return DataTables::of($records)
      ->addIndexColumn()
      
      ->addColumn('order_no',function($row){
         $getOrderDetails = order::where('id',$row->order_id)->first();
         return isset($getOrderDetails->orderno)?$getOrderDetails->orderno:'' ;
      })
       ->addColumn('customer_name',function($row){
        $getOrderDetails = order::where('id',$row->order_id)->first();
        return isset($getOrderDetails->name)?$getOrderDetails->name:'' ;
      })
      ->addColumn('user_name',function($row){
        $getUserName = Admin::where('id',$row->user_id)->first();
        $userName = ($getUserName->firstname) . ($getUserName->lastname);
        return $userName;
      })
      ->addColumn('inswitch_time',function($row){

        $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time)) ;
        $issueSwitch = $row->receive_switch; 
        
        $outswitch_html = '<div class="row" style="font-size: 20px;">';
        $outswitch_html .=  '<p>'.$PermissionName = isset($row->receivePermission->name)? $row->receivePermission->name :''.'</p>';
        $outswitch_html .= '</div>';
        $outswitch_html .= '<div class="row" style="font-size:14px">';
        $outswitch_html .=  ' <p>'.isset($receiveTime)? $receiveTime:''.'</p>';
        $outswitch_html .= '</div>';
        return $outswitch_html;
      })
      ->addColumn('created_date',function($row){
          $createdDate = date('d-m-Y', strtotime($row->created_at));
          return $createdDate;
      })
      ->rawColumns([
         'order_no',
         'inswitch_time',
         'user_name',
         'created_date',
         'customer_name'
       ])
        ->make(true); 
    }


     return view('admin.reports.department-pending-report',compact('departments'));
   }
   public function departmentPerformanceReport(Request $request){

    if($request->ajax()){

      $startDate = $request->startDate;
      $endDate = $request->endDate;
     

      if($request->department == 0){
        $orders = order::where('order_status',11)->pluck('id');
        $order_history = Order_history::whereIn('order_id',$orders)->get();
        if(isset($startDate) && isset($endDate)){
          $order_history = $order_history->whereBetween('created_at', [$startDate, $endDate]);
        }
       
      }else{
        $orders = order::where('order_status',11)->pluck('id');
        $order_history = Order_history::whereIn('order_id',$orders)->get();
        $order_history = $order_history->where('user_type', request('department'));
        if(isset($startDate) && isset($endDate)){
          $order_history = $order_history->whereBetween('created_at', [$startDate, $endDate]);
        }
      }
      return DataTables::of($order_history)
      ->addIndexColumn()
      ->addColumn('order_no',function($row){
         $orderId = $row->order_id;
         $getOrderNo = order::where('id',$orderId)->first();
         return isset($getOrderNo->orderno)?$getOrderNo->orderno :'';
      })
      ->addColumn('inswitch_time',function($row){
        $getReceiveDate = isset($row->receive_time)? $row->receive_time:'';
        if($getReceiveDate == null){
          $receiveTime = 'Not Received Order...';
        }else{
          $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time)) ;
        }
       
        $PermissionName = isset($row->receivePermission)? $row->receivePermission->name :'';
        $outswitch_html = '<div class="row" style="font-size:20px;">';
        $outswitch_html .=  '<span>'.$PermissionName.'</span>';
        $outswitch_html .= '</div>';
        $outswitch_html .= '<div class="row" style="font-size:14px;">';
        $outswitch_html .=  ' <span>'.$receiveTime.'</span>';
        $outswitch_html .= '</div>';
        return $outswitch_html;
      })
      ->addColumn('outswitch_time',function($row){

        $getIssueDate = isset($row->issue_time)? $row->issue_time:'';
        if($getIssueDate == null){
          $issueTime = 'In Working...';
        }else{
          $issueTime =  date('d-m-Y h:i:s', strtotime($getIssueDate)); 
        }
        $PermissionName = isset($row->issuePermission)? $row->issuePermission->name:'';
        $outswitch_html = '<div class="row" style="font-size:20px;">';
        $outswitch_html .=  '<span>'.$PermissionName.'</span>';
        $outswitch_html .= '</div>';
        $outswitch_html .= '<div class="row" style="font-size:14px;">';
        $outswitch_html .=  ' <span>'.$issueTime.'</span>';
        $outswitch_html .= '</div>';
        return $outswitch_html;
      })
      ->addColumn('duration',function($row){
           
        $taskTime = Task_manage::where('types_of_works',$row->typesofwork_id)
                                    ->where('task1_id',$row->receive_switch)
                                    ->where('task2_id', $row->switch_type)
                                    ->first();
                
                
            $taskHours = isset($taskTime->working_hours) ? (int)$taskTime->working_hours : 0;
            $taskMinutes = isset($taskTime->working_minutes) ? (int)$taskTime->working_minutes : 0;
            $taskSeconds = isset($taskTime->working_seconds) ? (int)$taskTime->working_seconds : 0;

            $time = Carbon::createFromTime($taskHours, $taskMinutes, $taskSeconds);
            $formattedTime = $time->format('H:i:s');

             
            $startDateTime = Carbon::parse($row->receive_time) ;
            $endDateTime =Carbon::parse($row->issue_time) ;
            
            $difference = $endDateTime->diff($startDateTime)->format('%H:%i:%s');
            $formattedTimeInSeconds = strtotime($formattedTime);
            $differenceInSeconds = strtotime($difference);
            
             if ($differenceInSeconds >= $formattedTimeInSeconds) {
              // If true, return the difference time in red font
              $diff = '<span style="color: red;">' .$endDateTime->diff($startDateTime)->format('%d days %h hours %i minute'). '</span>';
              } else {
                  // If false, return the difference time in green font
                  $diff = '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute'). '</span>';
              }
            return $diff;
    })
    ->addColumn('created_date',function($row){
      $createdDate = date('d-m-Y', strtotime($row->created_at));
      return $createdDate;
    })
      ->rawColumns(['inswitch_time','outswitch_time','duration','order_no','created_date'])
      ->make(true);
    }
     $departments= Role::get();
     return view('admin.reports.department-performance-report',compact('departments'));
   }
  
}
