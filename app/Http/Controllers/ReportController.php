<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\GeneralSetting;
use App\Models\order;
use App\Models\Order_history;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Task_manage;
use App\Models\types_work;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use PhpParser\Node\Stmt\Return_;

class ReportController extends Controller
{

    // Function for Get Report for Order History
    public function orderHistoryReport(Request $request)
    {
        if ($request->ajax())
        {

            $order_no = $request->order_number;
            $orders = order::query();

            if(!empty($order_no)){
                $orders = $orders->where('orderno', 'LIKE', '%'.$order_no.'%');
            }else{
                $orders = $orders->latest();
            }
            $orders = $orders->get();

            return DataTables::of($orders)
            ->addIndexColumn()
            ->addColumn('current_department', function ($row){
                if($row->order_status == 11) {
                    return "<span class='badge bg-success '>Order Delivered</span>";
                }elseif ($row->order_status == 12) {
                    return "<span class='badge bg-warning'>Selling</span>";
                }else {
                    $department = (isset($row['department']['name'])) ? $row['department']['name'] : '';
                    return $department;
                }
            })
            ->addColumn('duration', function ($row){
                if ($row->order_status == 11 || $row->order_status == 12){
                    $permission = Permission::where('name', 'delivery/complete')->first();
                    $permission2 =  Permission::where('name','iss.for.saleing')->first();
                    $order_histories = Order_history::where('order_id', $row->id)->get();

                    foreach($order_histories as $order_history){
                        //check in orderhistory records switch type = delivery
                        if($order_history->switch_type == $permission->id || $order_history->switch_type == $permission2->id){

                            $order_date = Carbon::parse($row->created_at);
                            $delivery_date = Carbon::parse($row->deliverydate);
                            $completed_date = Carbon::parse($order_history->issue_time);

                            // Check if order complete date is greater than or equal to delivery date
                            if($completed_date <= $delivery_date){
                                return '<span style="color: green;">' . $completed_date->diff($order_date)->format('%d days %h hours %i minute') . '</span>';
                            }else{
                                return '<span style="color: red;">' . $completed_date->diff($order_date)->format('%d days %h hours %i minute') . '</span>';
                            }
                        }
                    }
                }else{
                    return '<span class="badge bg-warning">Pending.</span>';
                }
            })
            ->addColumn('actions', function ($row) {
                $action_html = '<a href="' . route("reports.order_history_details", $row->id) . '" class="btn btn-sm custom-btn"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                return $action_html;
            })
            ->rawColumns(['actions', 'current_department', 'duration'])
            ->make(true);
        }
        return view('admin.reports.order_history_report');
    }

    // Function for Get Details of Order History Report
    public function orderHistoryReportDetails(Request $request, $id)
    {
        if($request->ajax())
        {
            $order_histories = Order_history::where('order_id', $id)->with('receivePermission', 'issuePermission','order','user')->get();

            return DataTables::of($order_histories)
            ->addIndexColumn()
            ->addColumn('order_no', function ($row) {
                return isset($row->order->orderno) ? $row->order->orderno : '';
            })

            ->addColumn('inswitch_time', function ($row) {
                $receive_date = isset($row->receive_time) ? $row->receive_time : '';
                if (!empty($receive_date) || $receive_date != '' || $receive_date != NULL) {
                    $receive_date = date('d-m-Y h:i:s', strtotime($row->receive_time));
                } else {
                    $receive_date = 'Not Received Order!';
                }
                $permission = isset($row->receivePermission) ? $row->receivePermission->name : '';
                $outswitch_html = '<div class="row" style="font-size:20px;"><span>' . $permission . '</span></div>';
                $outswitch_html .= '<div class="row" style="font-size:14px;"><span>' . $receive_date . '</span></div>';
                return $outswitch_html;
            })
            ->addColumn('outswitch_time', function ($row) {
                $issue_date = isset($row->issue_time) ? $row->issue_time : '';
                if (!empty($issue_date) || $issue_date != '' || $issue_date != NULL) {
                    $issue_date =  date('d-m-Y h:i:s', strtotime($issue_date));
                } else {
                    $issue_date = 'In Working...';
                }

                $permission = isset($row->issuePermission) ? $row->issuePermission->name : '';
                $outswitch_html = '<div class="row" style="font-size:20px;"><span>' . $permission . '</span></div>';
                $outswitch_html .= '<div class="row" style="font-size:14px;"><span>' . $issue_date . '</span></div>';
                return $outswitch_html;
            })
            ->addColumn('user_name', function ($row) {
                $firstname = (isset($row['user']['firstname'])) ? $row['user']['firstname'] : '';
                $lastname = (isset($row['user']['lastname'])) ? $row['user']['lastname'] : '';
                return $firstname." ".$lastname;
            })
            ->editColumn('reason_for_late',function($row){
                $reason = $row->reason_for_late;
                if(isset($reason)){
                   return $reason;
                }else{
                    return "No Delay";
                }
            })
            // ->addColumn('duration', function ($row) {

            //     $getOfficeTime = GeneralSetting::first();
            //     $officeStartTime = Carbon::parse(isset($getOfficeTime->StartTime)?$getOfficeTime->StartTime:'11:00:00');
            //     $officeEndTime = Carbon::parse(isset($getOfficeTime->EndTime)?$getOfficeTime->EndTime:'20:00:00');

            //     $task_details = Task_manage::where('types_of_works', $row->typesofwork_id)
            //     ->where('task1_id', $row->receive_switch)
            //     ->where('task2_id', $row->switch_type)
            //     ->first();

            //     $taskHours = isset($task_details->working_hours) ? (int)$task_details->working_hours : 0;
            //     $taskMinutes = isset($task_details->working_minutes) ? (int)$task_details->working_minutes : 0;
            //     $taskSeconds = isset($task_details->working_seconds) ? (int)$task_details->working_seconds : 0;

            //     $time = Carbon::createFromTime($taskHours, $taskMinutes, $taskSeconds);
            //     $formattedTime = $time->format('H:i:s');

            //     $startDateTime = Carbon::parse($row->receive_time);
            //     $endDateTime = Carbon::parse($row->issue_time);

            //     $difference = $endDateTime->diff($startDateTime)->format('%H:%i:%s');
            //     $formattedTimeInSeconds = strtotime($formattedTime);
            //     $differenceInSeconds = strtotime($difference);

            //     if ($differenceInSeconds >= $formattedTimeInSeconds) {
            //         // If true, return the difference time in red font
            //         $diff = '<span style="color: red;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
            //     } else {
            //         // If false, return the difference time in green font
            //         $diff = '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
            //     }
            //     return $diff;
            // })
            // ->addColumn('duration', function ($row) {

            //     $getOfficeTime = GeneralSetting::first();
            //     $officeStartTime = Carbon::parse(isset($getOfficeTime->StartTime)?$getOfficeTime->StartTime:'11:00:00');
            //     $officeEndTime = Carbon::parse(isset($getOfficeTime->EndTime)?$getOfficeTime->EndTime:'20:00:00');
            //     $officeDuration = $officeEndTime->diff($officeStartTime)->format('%H:%i:%s');

            //     $officeDurationInSeconds = strtotime($officeDuration);
            //     $task_details = Task_manage::where('types_of_works', $row->typesofwork_id)
            //         ->where('task1_id', $row->receive_switch)
            //         ->where('task2_id', $row->switch_type)
            //         ->first();

            //     $taskHours = isset($task_details->working_hours) ? (int) $task_details->working_hours : 0;
            //     $taskMinutes = isset($task_details->working_minutes) ? (int) $task_details->working_minutes : 0;
            //     $taskSeconds = isset($task_details->working_seconds) ? (int) $task_details->working_seconds : 0;

            //     $time = Carbon::createFromTime($taskHours, $taskMinutes, $taskSeconds);
            //     $formattedTime = $time->format('H:i:s');

            //     $formattedTimeInSeconds = strtotime($formattedTime);

            //     $startDateTime = isset($row->receive_time)? Carbon::parse($row->receive_time) : Carbon::parse($row->receive_time);
            //     $endDateTime = isset($row->issue_time)? Carbon::parse($row->issue_time): Carbon::parse($row->issue_time);

            //     $difference = $endDateTime->diff($startDateTime)->format('%H:%i:%s');
            //     $differenceInSeconds = strtotime($difference);


            //     if($differenceInSeconds > $officeDurationInSeconds){

            //         $adjustedDifferenceInSeconds = $differenceInSeconds - $officeDurationInSeconds;
            //         $adjustedDifferenceInSeconds = $differenceInSeconds - $adjustedDifferenceInSeconds;
            //         $adjustedDifferenceInSeconds = ($differenceInSeconds - $adjustedDifferenceInSeconds);
            //         $countableTime = gmdate('H:i:s',$adjustedDifferenceInSeconds);
            //         $countableTimeInSecond = strtotime($countableTime);
            //         $countableTime = Carbon::parse($countableTime);


            //             $days = $countableTime->diffInDays();
            //             $hours = $countableTime->diffInHours() - ($days * 24);  // Subtract the hours already counted in days
            //             $minutes = $countableTime->diffInMinutes() - ($days * 24 * 60) - ($hours * 60);  // Subtract the minutes already counted in days and hours
            //             $result = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);

            //             if($countableTimeInSecond >= $formattedTimeInSeconds){
            //                $result = '<span style="color: red;">' .$result . '</span>';
            //             }else{
            //                 $result = '<span style="color: green;">' .$result. '</span>';
            //             }

            //          return $result;
            //     }

            //     if ($differenceInSeconds >= $formattedTimeInSeconds) {
            //             // If true, return the difference time in red font
            //         $diff = '<span style="color: red;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
            //     } else {
            //         // If false, return the difference time in green font
            //         $diff = '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
            //     }
            //     return $diff;
            // })
            ->addColumn('duration', function ($row) {
                $getOfficeTime = GeneralSetting::first();
                $officeStartTime = Carbon::parse($getOfficeTime->StartTime ?? '11:00:00');
                $officeEndTime = Carbon::parse($getOfficeTime->EndTime ?? '20:00:00');
                $officeDurationInSeconds = $officeEndTime->diffInSeconds($officeStartTime);

                $task_details = Task_manage::where('types_of_works', $row->typesofwork_id)
                    ->where('task1_id', $row->receive_switch)
                    ->where('task2_id', $row->switch_type)
                    ->first();

                $taskHours = $task_details->working_hours ?? 0;
                $taskMinutes = $task_details->working_minutes ?? 0;
                $taskSeconds = $task_details->working_seconds ?? 0;

                $formattedTimeInSeconds = $taskHours * 3600 + $taskMinutes * 60 + $taskSeconds;

                $startDateTime = Carbon::parse($row->receive_time ?? Carbon::now());
                $endDateTime = Carbon::parse($row->issue_time ?? Carbon::now());

                $differenceInSeconds = $endDateTime->diffInSeconds($startDateTime);

                if ($differenceInSeconds > $officeDurationInSeconds) {
                    $adjustedDifferenceInSeconds = $differenceInSeconds - $officeDurationInSeconds;
                    $countableTime = Carbon::createFromTimestamp($adjustedDifferenceInSeconds)->setTimezone('UTC');

                    $days = floor($countableTime->day - 1); // subtracting 1 because day is 1-based
                    $hours = floor($countableTime->hour);
                    $minutes = floor($countableTime->minute);
                    $result = sprintf('%d days %d hours %d minutes', $days, $hours, $minutes);

                    if ($adjustedDifferenceInSeconds >= $formattedTimeInSeconds) {
                        return '<span style="color: red;">' . $result . '</span>';
                    } else {
                        return '<span style="color: green;">' . $result . '</span>';
                    }
                }

                if ($differenceInSeconds >= $formattedTimeInSeconds) {
                    return '<span style="color: red;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
                } else {
                    return '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
                }
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
        return view('admin.reports.order_history_report_details', compact('id'));
    }

    // Function for Get Department Pending Orders Report
    public function departmentPendingOrdersReport(Request $request)
    {
        // Get all Departments
        $departments = Role::get();

        if ($request->ajax())
        {
            $start_date = $request->startDate;
            $end_date = $request->endDate;
            $department_id = $request->department;

            $order_histories = Order_history::with('receivePermission', 'issuePermission' , 'department','order');
            $order_histories = $order_histories->whereNotNull('receive_time')
            ->whereNull('issue_time')
            ->whereNull('switch_type');

            if($department_id != 0)
            {
                $order_histories = $order_histories->where('user_type', $department_id);
            }

            if ((isset($start_date) && !empty($start_date)) && (isset($end_date)) && !empty($end_date))
            {
                $order_histories = $order_histories->whereBetween('created_at', [$start_date, $end_date]);
            }

            $order_histories = $order_histories->get();
            return DataTables::of($order_histories)
            ->addIndexColumn()
            ->addColumn('order_no', function ($row) {
                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->orderno) ? $getOrderDetails->orderno : '';
            })
            ->addColumn('customer_name', function ($row) {
                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->name) ? $getOrderDetails->name : '';
            })
            ->addColumn('mobile_no', function ($row) {

                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->mobile) ? $getOrderDetails->mobile : '';
            })
            ->addColumn('user_name', function ($row) {
                $getUserName = Admin::where('id', $row->user_id)->first();
                $userName = ($getUserName->firstname) . ($getUserName->lastname);
                return $userName;
            })
            ->addColumn('inswitch_time', function ($row) {
                $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time));
                $issueSwitch = $row->receive_switch;
                $outswitch_html = '<div class="row" style="font-size: 20px;">';
                $outswitch_html .=  '<p>' .isset($row->receivePermission->name) ? $row->receivePermission->name : '' . '</p>';
                $outswitch_html .= '</div>';
                $outswitch_html .= '<div class="row" style="font-size:14px">';
                $outswitch_html .=  ' <p>' . isset($receiveTime) ? $receiveTime : '' . '</p>';
                $outswitch_html .= '</div>';
                return $outswitch_html;
            })
            ->addColumn('remainig_time', function ($row)
            {
                $out_switch = $row->department->permissions()->where('permission_type',2)->get();
                $out_switch_id = (isset($out_switch[0]['id'])) ? $out_switch[0]['id'] : '';
                $taskdetails = Task_manage::where('types_of_works', $row->typesofwork_id)->where('task2_id', $out_switch_id)->first();

                $switch_in_date_time = $row->receive_time;
                $working_hours = (isset($taskdetails['working_hours'])) ? $taskdetails['working_hours'] : 00;
                $working_minutes = (isset($taskdetails['working_minutes'])) ? $taskdetails['working_minutes'] : 00;
                $working_seconds = (isset($taskdetails['working_seconds'])) ? $taskdetails['working_seconds'] : 00;

                // // Convert the string to a Carbon instance
                $carbonDate = Carbon::parse($switch_in_date_time);
                $currentDateTime = Carbon::now();

                $working_days_array = [];
                $general_settings = GeneralSetting::where('holiday', 'on')->get();
                $office_start_time = isset($general_settings[0]->StartTime)?$general_settings[0]->StartTime:'';
                $office_end_time = isset($general_settings[0]->EndTime)?$general_settings[0]->EndTime:'';
                // dd($office_start_time,$office_end_time);
                $office_start_time_array = explode(':', $office_start_time);
                $office_end_time_array = explode(':', $office_end_time);

                if(count($general_settings) > 0){
                    foreach($general_settings as $g_setting){
                        $day = (isset($g_setting['Days'])) ? $g_setting['Days'] : '';
                        if($day == 'monday'){
                            $working_days_array[$day] = 1;
                        }elseif($day == 'tuesday'){
                            $working_days_array[$day] = 2;
                        }elseif($day == 'wednesday'){
                            $working_days_array[$day] = 3;
                        }elseif($day == 'thursday'){
                            $working_days_array[$day] = 4;
                        }elseif($day == 'friday'){
                            $working_days_array[$day] = 5;
                        }elseif($day == 'saturday'){
                            $working_days_array[$day] = 6;
                        }elseif($day == 'sunday'){
                            $working_days_array[$day] = 0;
                        }
                    }
                }


                $total_work_seconds = $working_hours * 3600 + $working_minutes * 60 + $working_seconds;

                // Calculate remaining office time for the current day
                $remainingOfficeTime = $carbonDate->copy()->setTime($office_end_time_array[0], $office_end_time_array[1], $office_end_time_array[2])->diffInSeconds($carbonDate);

                // Check if the current time is outside office hours
                if ($carbonDate->lt($carbonDate->setTime($office_start_time_array[0], $office_start_time_array[1], $office_start_time_array[2])) || $carbonDate->gte($carbonDate->setTime($office_end_time_array[0], $office_end_time_array[1], $office_end_time_array[2]))) {
                    $carbonDate = $carbonDate->copy()->setTime($office_start_time_array[0], $office_start_time_array[1], $office_start_time_array[2]);
                }

                // Check if the current day is a working day
                $currentDay = strtolower($carbonDate->format('l'));

                if (!in_array($currentDay, array_keys($working_days_array))) {
                    // Find the next working day
                    do {
                        $carbonDate->addDay();
                        $currentDay = strtolower($carbonDate->format('l'));
                    } while (!in_array($currentDay, array_keys($working_days_array)));
                }

                // Use a while loop to deduct working time from remaining office time
                while ($total_work_seconds > $remainingOfficeTime) {
                    // Deduct remaining office time from total working time
                    $total_work_seconds -= $remainingOfficeTime;

                    // Move to the next working day
                    $carbonDate->addDay();
                    $currentDay = strtolower($carbonDate->format('l'));
                    while (!in_array($currentDay, array_keys($working_days_array))) {
                        $carbonDate->addDay();
                        $currentDay = strtolower($carbonDate->format('l'));
                    }

                    // Set the time to the office start time
                    $carbonDate->setTime($office_start_time_array[0], $office_start_time_array[1], $office_start_time_array[2]);

                    // Calculate remaining office time for the new day
                    $remainingOfficeTime = $carbonDate->copy()->setTime($office_end_time_array[0], $office_end_time_array[1], $office_end_time_array[2])->diffInSeconds($carbonDate);
                }

                $delivery_date = $carbonDate->addSeconds($total_work_seconds)->format('d-m-Y H:i:s');

                // Check if the current date and time is after the specified date and time
                if ($currentDateTime->gt($carbonDate)) {
                    $remainingTime = '<span class="text-danger">Delay Time</span>';
                } else {
                    // Calculate the remaining time
                    $diff = $currentDateTime->diff($carbonDate);
                    $remainingTime = $diff->days . ' days, ' . $diff->h . ' hours, ' . $diff->i . ' minutes, ' . $diff->s . ' seconds';
                }

                return $remainingTime;
            })
            ->rawColumns([
                'order_no',
                'inswitch_time',
                'user_name',
                'remainig_time',
                'customer_name',
                'mobile_no'
            ])
            ->make(true);
        }

        return view('admin.reports.department_pending_orders_report', compact('departments'));
    }

    // Function for Get Types of Works Pending Report
    public function typesOfWorksPendingReport(Request $request)
    {
        // Get all Types of Works
        $types_of_works = types_work::get();

        if ($request->ajax())
        {
            $type_of_work_id = $request->type_of_work;

            $order_histories = Order_history::with('receivePermission', 'issuePermission' , 'department');
            $order_histories = $order_histories->whereNotNull('receive_time')
            ->whereNull('issue_time')
            ->whereNull('switch_type');

            if($type_of_work_id != 0)
            {
                $order_histories = $order_histories->where('typesofwork_id', $type_of_work_id);
            }

            $order_histories = $order_histories->get();

            return DataTables::of($order_histories)
            ->addIndexColumn()
            ->addColumn('order_no', function ($row) {
                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->orderno) ? $getOrderDetails->orderno : '';
            })
            ->addColumn('customer_name', function ($row) {
                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->name) ? $getOrderDetails->name : '';
            })
            ->addColumn('mobile_no', function ($row) {

                $getOrderDetails = order::where('id', $row->order_id)->first();
                return isset($getOrderDetails->mobile) ? $getOrderDetails->mobile : '';
            })
            ->addColumn('user_name', function ($row) {
                $getUserName = Admin::where('id', $row->user_id)->first();
                $userName = ($getUserName->firstname) . ($getUserName->lastname);
                return $userName;
            })
            ->addColumn('inswitch_time', function ($row) {
                $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time));
                $issueSwitch = $row->receive_switch;
                $outswitch_html = '<div class="row" style="font-size: 20px;">';
                $outswitch_html .=  '<p>' .isset($row->receivePermission->name) ? $row->receivePermission->name : '' . '</p>';
                $outswitch_html .= '</div>';
                $outswitch_html .= '<div class="row" style="font-size:14px">';
                $outswitch_html .=  ' <p>' . isset($receiveTime) ? $receiveTime : '' . '</p>';
                $outswitch_html .= '</div>';
                return $outswitch_html;
            })
            ->addColumn('remainig_time', function ($row)
            {
                $delivery_date = $row->order['deliverydate'];
                $currentTime = Carbon::now();
                $delivery_date_time = Carbon::parse($delivery_date);

                // Calculate remaining time
                $remainingTime = $currentTime->diff($delivery_date_time);
                $remain_del_date = $currentTime->copy()->add($remainingTime);

                 if ($currentTime->gt($remain_del_date)) {
                    $remainingTime = '<span class="text-danger">Delay Time</span>';
                } else {
                    // Calculate the remaining time
                    $diff = $currentTime->diff($remain_del_date);
                    $remainingTime = $diff->days . ' days, ' . $diff->h . ' hours, ' . $diff->i . ' minutes, ' . $diff->s . ' seconds';
                }
                return $remainingTime;
            })
            ->addColumn('actions',function($row){
                $orderNo = $row->order['orderno'];
                $action_html = '<a href='.route("order.retrive",["id" => $orderNo ]) .' class="btn btn-sm custom-btn rounded-circle" ><i class="fa fa-info-circle" aria-hidden="true"></i></a>';
                $action_html .= '<a href="' . route("reports.order_history_details", $row->order_id) . '" class="btn btn-sm custom-btn" ><i class="fa fa-list-alt" aria-hidden="true"></i></a>';
                return $action_html;
            })
            ->rawColumns([
                'order_no',
                'inswitch_time',
                'user_name',
                'remainig_time',
                'customer_name',
                'mobile_no',
                'actions'
            ])
            ->make(true);

        }

        return view('admin.reports.types_of_works_pending_report', compact(['types_of_works']));
    }


    public function departmentPerformanceReport(Request $request)
    {

        if ($request->ajax()) {

            $startDate = $request->startDate;
            $endDate = $request->endDate;


            if ($request->department == 0) {
                $orders = order::where('order_status', 11)->pluck('id');
                $order_history = Order_history::whereIn('order_id', $orders)->get();
                if (isset($startDate) && isset($endDate)) {
                    $order_history = $order_history->whereBetween('created_at', [$startDate, $endDate]);
                }
            } else {
                $orders = order::where('order_status', 11)->pluck('id');
                $order_history = Order_history::whereIn('order_id', $orders)->get();
                $order_history = $order_history->where('user_type', request('department'));
                if (isset($startDate) && isset($endDate)) {
                    $order_history = $order_history->whereBetween('created_at', [$startDate, $endDate]);
                }
            }
            return DataTables::of($order_history)
                ->addIndexColumn()
                ->addColumn('order_no', function ($row) {
                    $orderId = $row->order_id;
                    $getOrderNo = order::where('id', $orderId)->first();
                    return isset($getOrderNo->orderno) ? $getOrderNo->orderno : '';
                })
                ->addColumn('mobile_no', function ($row) {

                    $getOrderDetails = order::where('id', $row->order_id)->first();
                    return isset($getOrderDetails->mobile) ? $getOrderDetails->mobile : '';
                })
                ->addColumn('inswitch_time', function ($row) {
                    $getReceiveDate = isset($row->receive_time) ? $row->receive_time : '';
                    if ($getReceiveDate == null) {
                        $receiveTime = 'Not Received Order...';
                    } else {
                        $receiveTime = date('d-m-Y h:i:s', strtotime($row->receive_time));
                    }

                    $PermissionName = isset($row->receivePermission) ? $row->receivePermission->name : '';
                    $outswitch_html = '<div class="row" style="font-size:20px;">';
                    $outswitch_html .=  '<span>' . $PermissionName . '</span>';
                    $outswitch_html .= '</div>';
                    $outswitch_html .= '<div class="row" style="font-size:14px;">';
                    $outswitch_html .=  ' <span>' . $receiveTime . '</span>';
                    $outswitch_html .= '</div>';
                    return $outswitch_html;
                })
                ->addColumn('outswitch_time', function ($row) {

                    $getIssueDate = isset($row->issue_time) ? $row->issue_time : '';
                    if ($getIssueDate == null) {
                        $issueTime = 'In Working...';
                    } else {
                        $issueTime =  date('d-m-Y h:i:s', strtotime($getIssueDate));
                    }
                    $PermissionName = isset($row->issuePermission) ? $row->issuePermission->name : '';
                    $outswitch_html = '<div class="row" style="font-size:20px;">';
                    $outswitch_html .=  '<span>' . $PermissionName . '</span>';
                    $outswitch_html .= '</div>';
                    $outswitch_html .= '<div class="row" style="font-size:14px;">';
                    $outswitch_html .=  ' <span>' . $issueTime . '</span>';
                    $outswitch_html .= '</div>';
                    return $outswitch_html;
                })
                ->addColumn('duration', function ($row) {

                    $officeStartTime = Carbon::parse('11:00 am');
                    $officeEndTime = Carbon::parse('8:00 pm');

                    $taskTime = Task_manage::where('types_of_works', $row->typesofwork_id)
                        ->where('task1_id', $row->receive_switch)
                        ->where('task2_id', $row->switch_type)
                        ->first();


                    $taskHours = isset($taskTime->working_hours) ? (int)$taskTime->working_hours : 0;
                    $taskMinutes = isset($taskTime->working_minutes) ? (int)$taskTime->working_minutes : 0;
                    $taskSeconds = isset($taskTime->working_seconds) ? (int)$taskTime->working_seconds : 0;

                    $time = Carbon::createFromTime($taskHours, $taskMinutes, $taskSeconds);
                    $formattedTime = $time->format('H:i:s');

                    $startDateTime = Carbon::parse($row->receive_time);
                    $endDateTime = Carbon::parse($row->issue_time);

                       // Ensure the start time is not before the office start time
                        if ($startDateTime->lt($officeStartTime)) {
                            $startDateTime = $officeStartTime;
                        }

                        // Ensure the end time is not after the office end time
                        if ($endDateTime->gt($officeEndTime)) {
                            $endDateTime = $officeEndTime;
                        }

                    $difference = $endDateTime->diff($startDateTime)->format('%H:%i:%s');
                    $formattedTimeInSeconds = strtotime($formattedTime);
                    $differenceInSeconds = strtotime($difference);

                    if ($differenceInSeconds >= $formattedTimeInSeconds) {
                        // If true, return the difference time in red font
                        $diff = '<span style="color: red;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
                    } else {
                        // If false, return the difference time in green font
                        $diff = '<span style="color: green;">' . $endDateTime->diff($startDateTime)->format('%d days %h hours %i minute') . '</span>';
                    }
                    return $diff;
                })
                ->addColumn('created_date', function ($row) {
                    $createdDate = date('d-m-Y', strtotime($row->created_at));
                    return $createdDate;
                })
                ->rawColumns(['inswitch_time', 'outswitch_time', 'duration', 'order_no', 'created_date','mobile_no'])
                ->make(true);
        }
        $departments = Role::get();
        return view('admin.reports.department-performance-report', compact('departments'));
    }

    public function trassedOrders(Request $request){
       return redirect()->back()->with('error','work In Process On Trassed');
    }
}
