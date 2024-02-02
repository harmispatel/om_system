<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\{Role};
use App\Models\order;
use App\Models\orderimage;
use App\Models\hadleby;
use App\Models\customer_name;
use App\Models\{Block_reason, Order_history, Reason, Task_manage,Admin};
use App\Models\types_work;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\GeneralSetting;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:order', ['only' => ['index']]);
    }

    public function mobileSetName(Request $request){

        if($request->ajax()){
            $mobilenumber = $request->input('mobile');

            $customerName = customer_name::where('mobileNumber', $mobilenumber)->first();

            if ($customerName) {

                $customernameUpdate = customer_name::find($customerName->id);

                return response()->json(['customerName' => $customerName->customerName]);

            } else {

                return response()->json(['customerName' => null]);
            }
        }
    }

    public function fetchdata(Request $request){

        if($request->ajax()){

            $data = hadleby::distinct()->pluck('handleby');

            return response()->json($data);

        }
    }

    function calculateFinishDateTime($totalHours, $startDateTime)
    {
        $currentDateTime = Carbon::parse($startDateTime);
        $currentDate = Carbon::now();
        $generalSetting = GeneralSetting::get();

        $startTime = Carbon::parse($generalSetting[0]->StartTime)->format('H');
        $endTime = Carbon::parse($generalSetting[0]->EndTime)->format('H');

        $hoursWorked = 0;

        while ($hoursWorked < $totalHours) {
            // Add one hour to the current date and time
            $currentDateTime->addHour();

            // Retrieve the GeneralSetting record for the current day
            $currentDaySetting = GeneralSetting::where('Days', $currentDate->englishDayOfWeek)->first();

            $dayName = $currentDateTime->formatLocalized('%A');

            $isHoliday = GeneralSetting::where('Days', $dayName)->first()->holiday;
            // Check if the current day is marked as "off" in the GeneralSetting table

            if ($isHoliday == 'on' && $currentDateTime->hour >= $startTime && $currentDateTime->hour <= $endTime) {
                $hoursWorked++;
            }

        }
        return $currentDateTime->toDateTimeString();
    }


    public function getData(Request $request)
    {
        if($request->ajax())
        {
            $qr_name = '';
            $order_no = '';
            $delivery_date = '';
            $type_of_work_id = $request->id;
            $current_day = GeneralSetting::where('Days', strtolower(Carbon::now()->format('l')))->first();
            $office_start_time = (isset($current_day['StartTime']) && !empty($current_day['StartTime'])) ? $current_day['StartTime'] : '11:00:00';
            $office_end_time = (isset($current_day['EndTime']) && !empty($current_day['EndTime'])) ? $current_day['EndTime'] : '20:00:00';

            $working_days_array = [];
            $general_settings = GeneralSetting::where('holiday', 'on')->get();
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

            if (!empty($type_of_work_id))
            {
                $type_of_work = types_work::find($type_of_work_id);
                $work_code = (isset($type_of_work['order_value'])) ? $type_of_work['order_value'] : '';
                $working_hours = (isset($type_of_work['working_hours'])) ? $type_of_work['working_hours'] : '00';
                $working_minutes = (isset($type_of_work['working_minutes'])) ? $type_of_work['working_minutes'] : '00';
                $working_seconds = (isset($type_of_work['working_seconds'])) ? $type_of_work['working_seconds'] : '00';

                $currentDateTime = Carbon::now();
                $office_start_time_array = explode(':', $office_start_time);
                $office_end_time_array = explode(':', $office_end_time);

                $totalWorkingSeconds = $working_hours * 3600 + $working_minutes * 60 + $working_seconds;

                // Check if the current time is outside office hours
                if ($currentDateTime->lt($office_start_time) || $currentDateTime->gte($office_end_time)) {
                    $currentDateTime = $currentDateTime->copy()->setTime($office_start_time_array[0], $office_start_time_array[1], $office_start_time_array[2]);
                }

                // Calculate remaining office time for the current day
                $remainingOfficeTime = $currentDateTime->copy()->setTime($office_end_time_array[0], $office_end_time_array[1], $office_end_time_array[2])->diffInSeconds($currentDateTime);

                // Check if the current day is a working day
                $currentDay = strtolower($currentDateTime->format('l'));
                if (!in_array($currentDay, array_keys($working_days_array))) {
                    // Find the next working day
                    do {
                        $currentDateTime->addDay();
                        $currentDay = strtolower($currentDateTime->format('l'));
                    } while (!in_array($currentDay, array_keys($working_days_array)));
                }

                // Use a while loop to deduct working time from remaining office time
                while ($totalWorkingSeconds > $remainingOfficeTime) {
                    // Deduct remaining office time from total working time
                    $totalWorkingSeconds -= $remainingOfficeTime;

                    // Move to the next working day
                    $currentDateTime->addDay();
                    $currentDay = strtolower($currentDateTime->format('l'));
                    while (!in_array($currentDay, array_keys($working_days_array))) {
                        $currentDateTime->addDay();
                        $currentDay = strtolower($currentDateTime->format('l'));
                    }

                    // Set the time to the office start time
                    $currentDateTime->setTime($office_start_time_array[0], $office_start_time_array[1], $office_start_time_array[2]);

                    // Calculate remaining office time for the new day
                    $remainingOfficeTime = $currentDateTime->copy()->setTime($office_end_time_array[0], $office_end_time_array[1], $office_end_time_array[2])->diffInSeconds($currentDateTime);
                }

                // Add the remaining working time to the current date and time
                $delivery_date = $currentDateTime->addSeconds($totalWorkingSeconds)->format('d-m-Y H:i:s');

                $max_order_id = order::max('id') + 1;
                $rand_number = rand(0, 9);
                $order_no = $work_code.$max_order_id.$rand_number;
                $qr_name = $order_no."_qr.svg";

                if(!empty($qr_name) && !file_exists('public/images/qrcodes/'.$qr_name)){

                    $qr_url = URL::to('/admin/ordersDetail/') . "/" . $order_no;
                    $upload_path = public_path('images/qrcodes/'.$qr_name);
                    QrCode::format('svg')->margin(2)->size(200)->generate($qr_url, $upload_path);
                }
            }

            return response()->json([
                'orderno' => $order_no,
                'qrcode_name' => $qr_name,
                'DeliveryDate' => $delivery_date,
            ]);
        }
    }

    // Display a listing of the resource.
    public function index(Request $request)
    {
        $currentDate = Carbon::now();
        $block_reasons = Block_reason::get();
        // Get the first day of the current month
        $firstDayOfCurrentMonth = $currentDate->copy()->subMonth();
        $lastDayOfLastMonth = $currentDate->copy()->addDay();

        if($request->ajax()){

            $user_type = Auth::guard('admin')->user()->user_type;
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);

            if($startDate != null && $endDate!= null){
                if ($startDate > $endDate) {
                    [$startDate, $endDate] = [$endDate, $startDate];
                }
                $orders = order::whereBetween('created_at',[$startDate,$endDate])->latest();

            }else{
                $orders = order::whereBetween('created_at', [$firstDayOfCurrentMonth, $lastDayOfLastMonth])->latest();
            }
            $orders->where('is_bloked','!=', 0)->where('orderno','!=',null);
            // if($user_type != 1){
            //     $orders = $orders->where('order_status',$user_type)->get();
            // }

            return DataTables::of($orders)
            ->addIndexColumn()
            ->editColumn('SelectOrder',function($orders){

                if ($orders->SelectOrder == 0) {
                    return 'New Order';
                } else  {
                    return 'Repeat Order';
                }
            })
            ->editColumn('order_status',function($orders){

                $getrole = Role::where('id', $orders->order_status)->first();

                if(!$getrole){

                    if($orders->order_status == '11'){
                        $html_button = "<span class='badge bg-success'>Delivery Completed</span>";
                        return $html_button;
                    }else{
                        $html_button = "<span class='badge bg-warning'>Saleing</span>";
                        return $html_button;
                    }
                }else{
                    return isset ($getrole->name) ? $getrole->name : '';
                }
            })
            ->addColumn('created_date', function ($row) {
                $createdDate = date('d-m-Y', strtotime($row->created_at));
                return $createdDate;
            })
            ->addColumn('block', function ($row)
            {
                $user_details =  Auth::guard('admin')->user();
                $is_bloked = $row->is_bloked;
                $checked = ($is_bloked == 1) ? 'checked' : '';
                $checkVal = ($is_bloked == 1) ? 0 : 1;
                $order_id = isset($row->id) ? $row->id : '';
                $diabled = ($order_id == 1) ? 'disabled' : '';
                if($user_details->user_type != 1){
                    return '-';
                }else{
                    return '<div class="form-check form-switch"><input class="form-check-input" type="checkbox" role="switch" onchange="BlockOrder('.$checkVal.','.$order_id.')" id="statusBtn" '.$checked.' '.$diabled.'></div>';
                }


            })
            ->addColumn('actions',function($row){
                $is_bloked = $row->is_bloked;
                $orderNo = isset($row->orderno) ? $row->orderno : '';
                $user_details =  Auth::guard('admin')->user();
                $user_type = (isset($user_details->user_type)) ? $user_details->user_type : '';

                $action_html = '';
                $action_html .= '<div class="d-flex">';
                    $action_html .= '<a href='.route("order.retrive",["id" => $orderNo ]) .' class="btn btn-sm btn-primary rounded-circle me-2"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    $action_html .= '<a href="'.route('reports.order_history_details', $row->id).'" class="btn btn-sm btn-primary rounded-circle me-2"><i class="fa fa-history" aria-hidden="true"></i></a>';

                    if($user_type == 1 || $user_type == 2){

                        if($is_bloked == 0){
                            $action_html .= '<button id="myButton" class="rounded-circle text-danger"disabled><i class="fa fa-ban" aria-hidden="true"></i></button>';
                        }else{
                            $action_html .= '<a onclick="blockOrderRecord(\''.$row->id.'\')" class="btn btn-sm btn-danger rounded-circle"><i class="fa fa-ban" aria-hidden="true"></i></a>';
                        }
                        // $action_html .= '<a onclick="deleteOrderRecord(\''.$orderNo.'\')" class="btn btn-sm btn-danger rounded-circle"><i class="fa fa-trash" aria-hidden="true"></i></a>';

                    }
                $action_html .= '</div>';
                return $action_html;
            })
            ->rawColumns(['actions','order_status', 'SelectOrder','created_date','block'])
            ->make(true);
        }
       return view('admin.orders.orders',compact(['firstDayOfCurrentMonth','lastDayOfLastMonth','block_reasons']));
    }

    public function blockOrder(Request $request){

        $id = $request->orderId;
        $u_id = Auth::guard('admin')->user()->id;

        $request->validate([
            'orderId' => 'required',
            'block_reason' => 'required_if:block_reason_other,null',
            'block_reason_other' => 'required_if:block_reason,null'
        ]);

        try{

            if($request->block_reason == '' || $request->block_reason == null){
                $inputReason = $request->block_reason_other;
            }else{
                $inputReason = $request->block_reason;
            }

            $input = order::find($id);
            $input->block_reason = $inputReason;
            $input->is_bloked = 0;
            $input->whos_block_order = $u_id;
            $input->update();

            return redirect()->back()->with('success','Order Blocked Successfully');

        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $counters = types_work::get();

        return view('admin.orders.create', compact('counters'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


           $request->validate([
                'counter_id' => 'required',
                'orderno' => 'required|unique:orders',
                'name' => 'required',
                'mobile' => 'required|digits:10',
                'SelectOrder' => 'required',
                'touch' => 'required',
                'gold' => 'required',
                'metal' => 'required',
                'handleby' => 'required',
                'orderimage' => 'required'
                ],
                [
                    'handleby.required' => 'This field is required',
                    'orderimage.required' => 'This field is required'
                ]
            );

        try{
            $input = $request->except('_token','orderimage');
            // $orders = order::create($input);
            $user_type = Auth::guard('admin')->user()->user_type;


             $user_id =  Auth::guard('admin')->user()->id;
             $user_type =  Auth::guard('admin')->user()->user_type;

             //     $orders = order::create($input);

            $orders = order::create([
             'counter_id' => $request->counter_id,
             'name' => $request->name,
             'mobile' => $request->mobile,
             'SelectOrder' => $request->SelectOrder,
             'touch' => $request->touch,
             'gold' => $request->gold,
             'metal' => $request->metal,
             'orderno' => $request->orderno,
             'charges' => $request->charges,
             'advance' => $request->advance,
             'metalwt' => $request->metalwt,
             'deliverydate' => $request->deliverydate,
             'handleby' => $request->handleby,
             'Qrphoto' => $request->Qrphoto,
             'order_status' => $user_type
            ]);
            $getPermissionId = Permission::where('name','new_order')->first();
            $orderhistry = Order_history::create([
                'order_id' => $orders->id,
                'user_id' => Auth::guard('admin')->user()->id, // Assuming the user is authenticated
                'user_type' => Auth::guard('admin')->user()->user_type,
                'typesofwork_id' =>  $orders->counter_id,
                'receive_time' => Carbon::now(),
                'receive_switch'=> $getPermissionId->id,


            ]);

            if($request->orderimage){
                 foreach($request->file('orderimage') as $imagephoto){
                     $orderimages = new orderimage;
                     $photoname = $imagephoto->getClientOriginalName();
                     $extenstion = $imagephoto->getClientOriginalExtension();
                     $imagephoto->move(public_path('orderimages'),$photoname);
                     $orderimages->orderimage = $photoname;
                     $orderimages->order_id = $orders->id;
                     $orderimages->save();
                 }
            }

            //    $order_status = Order_history::create([
            //     'order_id' => $orders->id,
            //     'user_id' => $user_id,
            //     'order_status' => $user_type,
            //     'user_type' => $user_type
            //    ]);


            $existingCustomer = customer_name::where('mobileNumber', $request->mobile)->first();

            if ($existingCustomer) {
                // Mobile number already exists, update the customer's name
                $existingCustomer->customerName = $request->name;
                $existingCustomer->update();
            } else {
                // Mobile number doesn't exist, create a new customer in customer_name table
                $customerNameInsert = new customer_name;
                $customerNameInsert->customerName = $request->name;
                $customerNameInsert->mobileNumber = $request->mobile; // Assuming 'mobileNumber' is the correct column name
                $customerNameInsert->save();
            }
                    if($request->handleby){
                        $handleby = new hadleby;
                        $handleby->handleby = $request->handleby;
                        $handleby->handle_id = $orders->id;
                        $handleby->save();

                    }


            return redirect()->route('order.retrive',['id'=>$orders->orderno])->with('success','Data Saved Successfully!');
           }catch (\Throwable $th) {
           dd($th);
            return redirect()->route('order')->with('error','Internal Server Error!');
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $orderdetail = order::where('orderno',$id)->first();

        $orderimages = orderimage::where('order_id',$orderdetail->id)->get();
        $getOfficeTime = GeneralSetting::first();
        $startDateTime = isset($getOfficeTime->StartTime)? $getOfficeTime->StartTime:'11:00:00';
        $endDateTime = isset($getOfficeTime->EndTime)? $getOfficeTime->EndTime:'20:00:00';
        $typesofworkId = types_work::where('id',$orderdetail->counter_id)->first();

        $userdetail = Auth::guard('admin')->user();
        $reasons = Reason::where('department',$userdetail->user_type)->get();
        try{

            $userId = Auth::guard('admin')->user()->id;
            $department_id = Role::where('id',$userdetail->user_type)->first();


            $existingScan = Order_history::where('order_id', $orderdetail->id)
            ->where('user_id', $userId)
            ->exists();

                $currentDateTime = Carbon::now();
                $currentTime = $currentDateTime->format('H:i:s');

                $isWithinTimeRange =   $currentTime > $startDateTime &&  $currentTime < $endDateTime;

            if(!$existingScan && $isWithinTimeRange){

                $orderhistry = Order_history::create([
                    'order_id' => $orderdetail->id,
                    'user_id' => Auth::guard('admin')->user()->id, // Assuming the user is authenticated
                    'user_type' => Auth::guard('admin')->user()->user_type,
                    'scan_date' => $currentDateTime,
                    'typesofwork_id' =>  $typesofworkId->id,

                ]);

            }
        }catch(\Throwable $th){
          dd($th->getMessage());
        }

        return view('admin.orders.orderDetail',compact('orderdetail','orderimages','typesofworkId','reasons'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printOrder($id){
        $order_details = order::where('orderno',$id)->first();

        $order_images = orderimage::where('order_id',$order_details->id)->get();

        $typeofwork = types_work::where('id',$order_details->counter_id)->first();
        return view('admin.orders.detailform',compact('order_details','order_images','typeofwork'));
    }
    public function oneForm($id){

        $orderdetail = order::where('orderno',$id)->first();

        $orderimages = orderimage::where('order_id',$orderdetail->id)->get();

        $typesofworkId = types_work::where('id',$orderdetail->counter_id)->first();
        return view('admin.orders.printform',compact('orderdetail','orderimages','typesofworkId'));
    }
    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }

    public function blockOrdersList(Request $request){

        if($request->ajax()){

            $blockOrders = order::where('is_bloked','=',0);
            return DataTables::of($blockOrders)
            ->addIndexColumn()
            ->addColumn('who_block_order',function($row){
                $get_userdetail = Admin::where('id',$row->whos_block_order)->first();
                $firstname = isset($get_userdetail->firstname) ? $get_userdetail->firstname:'';
                $lastname = isset($get_userdetail->lastname) ? $get_userdetail->lastname:'';
                return $firstname . $lastname;
            })
            ->editColumn('SelectOrder',function($orders){

                if ($orders->SelectOrder == 0) {
                    return 'New Order';
                } else  {
                    return 'Repeat Order';
                }
            })
            ->addColumn('actions',function($row){
                $is_bloked = $row->is_bloked;
                $orderNo = isset($row->orderno) ? $row->orderno : '';
                $user_details =  Auth::guard('admin')->user();
                $user_type = (isset($user_details->user_type)) ? $user_details->user_type : '';

                $action_html = '';
                $action_html .= '<div class="d-flex">';
                    $action_html .= '<a href='.route("order.retrive",["id" => $orderNo ]) .' class="btn btn-sm btn-primary rounded-circle me-2"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    $action_html .= '<a href="'.route('reports.order_history_details', $row->id).'" class="btn btn-sm btn-primary rounded-circle me-2"><i class="fa fa-history" aria-hidden="true"></i></a>';

                $action_html .= '</div>';
                return $action_html;
            })
            ->rawColumns(['actions','SelectOrder'])
            ->make(true);
        }
        return view('admin.orders.block-orders');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try
        {
            $order = order::where('orderno',$request->id)->delete();


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

    //for orderDetail get
    public function retriveOrder($id){

        $orderdetail = order::where('orderno',$id)->first();

        $orderimages = orderimage::where('order_id',$orderdetail->id)->get();

        $typesofworkId = types_work::where('id',$orderdetail->counter_id)->first();
        $reasons = Reason::where('department',Auth::guard('admin')->user()->user_type)->get();


        return view('admin.orders.orderDetail',compact('orderdetail','orderimages','typesofworkId','reasons'));
    }


    //check for delay time in issue
    private function checkDelay($order_id, $userType, $issue_switch) {

        $current_day = GeneralSetting::where('Days', strtolower(Carbon::now()->format('l')))->first();
        $office_start_time = (isset($current_day['StartTime']) && !empty($current_day['StartTime'])) ? $current_day['StartTime'] : '11:00:00';
        $office_end_time = (isset($current_day['EndTime']) && !empty($current_day['EndTime'])) ? $current_day['EndTime'] : '20:00:00';
        $general_settings = GeneralSetting::where('holiday', 'on')->get();

        $daysArray = [];

        foreach ($general_settings as $setting) {
            if (isset($setting->Days)) {
                $daysArray[] = $setting->Days;
            }
        }

        // Fetch the receive time and other relevant data for the given order_id and userType
        $getOrderhistory = Order_history::with('receivePermission', 'issuePermission' , 'department')
                            ->where('order_id', $order_id)->where('user_type', $userType)->first();

        $out_switch_id = $issue_switch;
        $taskdetails = Task_manage::where('types_of_works', $getOrderhistory->typesofwork_id)
                            ->where('task1_id',$getOrderhistory->receive_switch)
                            ->where('task2_id', $out_switch_id)->first();

        $switch_in_date_time = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;

        $working_hours = (isset($taskdetails['working_hours'])) ? $taskdetails['working_hours'] : 00;
        $working_minutes = (isset($taskdetails['working_minutes'])) ? $taskdetails['working_minutes'] : 00;
        $working_seconds = (isset($taskdetails['working_seconds'])) ? $taskdetails['working_seconds'] : 00;
        $timeFormatted = sprintf(
            "%02d:%02d:%02d",
            $working_hours,
            $working_minutes,
            $working_seconds
        );

        // // Convert the string to a Carbon instance
        $carbonDate = Carbon::parse($switch_in_date_time);
        $currentDateTime = Carbon::now();
        $totalDuration = Carbon::parse('00:00:00');

        if ($carbonDate->isSameDay($currentDateTime)) {

            $duration = $carbonDate->diffInMinutes($currentDateTime);
            $totalDuration->addMinutes($duration);

        } else {
            // For the start date
            $startDay = $carbonDate->copy();
            list($Ehours, $Eminutes, $Eseconds) = sscanf($office_end_time, "%d:%d:%d");
            $endOfDay = $startDay->copy()->setTime($Ehours, $Eminutes, $Eseconds);
            $duration = $startDay->diffInMinutes($endOfDay);
            $totalDuration->addMinutes($duration);


            // For the end date
            $endDay = $currentDateTime->copy();
            list($Shours, $Sminutes, $Sseconds) = sscanf($office_start_time, "%d:%d:%d");
            $startOfDay = $endDay->copy()->setTime($Shours, $Sminutes, $Sseconds);
            $duration = $startOfDay->diffInMinutes($endDay);
            $totalDuration->addMinutes($duration);

             // For days in between
            $currentDay = $startDay->copy()->addDay();
            while ($currentDay->lt($endDay)) {
                $dayName = strtolower($currentDay->format('l'));
                if (in_array($dayName, $daysArray)) {
                    $startTime = $currentDay->copy()->setTime($Shours, $Sminutes, $Sseconds);
                    $endTime = $currentDay->copy()->setTime($Ehours, $Eminutes, $Eseconds);
                    $duration = $startTime->diffInMinutes($endTime);
                    $totalDuration->addMinutes($duration);
                }
                $currentDay->addDay();
            }


        }

        $diffrenceOfSwitches = $totalDuration;
        // Check if the current date and time is after the specified date and time
        $diffrenceOfSwitches = $diffrenceOfSwitches->format('H:i:s');
        $diffrenceOfSwitches = strtotime($diffrenceOfSwitches);
        $timeFormatted = strtotime($timeFormatted);

        if ($diffrenceOfSwitches > $timeFormatted) {

            return [
                        'delay' => true,
                        'reason' => 'You are late in issuing the item.'
                    ];
        } else {

            return ['delay' => false];
        }

        return ['delay' => false];

    }
    public function lateReceive(Request $request){

       $request->validate([
        'order_id' => 'required',
        'permission_id' => 'required',
        'switch1_option' => 'required_if:switch1_text,null',
        'switch1_text' => 'required_if:switch1_option,null'
       ]);

       try{

            if($request->switch1_option == '' || $request->switch1_option == null){
                $inputReason = $request->switch1_text;
            }else{
                $inputReason = $request->switch1_option;
            }

            $order_id = $request->order_id;
            $getPermissionId = $request->permission_id;
            $reason = $inputReason;

            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();

            if(isset($getOrderhistory)){

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId;
                $update->late_receive_reason = $reason;
                $update->save();

            }

            return redirect()->back()->with('success','Receive Successfully With Your Reason For Late..');

        }catch(\Throwable $th){
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }
    public function lateIssue(Request $request){

        $request->validate([
            'order_id' => 'required',
            'department_id' => 'required',
            'permission_id' => 'required',
            'switch1_option' => 'required_if:switch1_text,null',
            'switch1_text' => 'required_if:switch1_option,null'
        ]);

        try{

            if($request->switch1_option == '' || $request->switch1_option == null){
                $inputReason = $request->switch1_text;
            }else{
                $inputReason = $request->switch1_option;
            }

            $order_id = $request->order_id;
            $departmentId = $request->department_id;
            $getPermissionId = $request->permission_id;
            $reason = $inputReason;
            $orderdetail = order::where('id',$order_id)->first();

            $orderdetail->order_status = $departmentId;
            $orderdetail->save();

            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $workId = $orderdetail->counter_id;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();


            if(empty($getOrderhistory)){

            $createNewOrderhistory = Order_history::create([
                'user_id' =>  Auth::guard('admin')->user()->id,
                'order_id' => $order_id ,
                'user_type' => $userType,
                'typesofwork_id' => $workId,
                'switch_type' =>  $getPermissionId,
                'issue_time' => Carbon::now(),
                'reason_for_late' => $reason,
            ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->issue_time = Carbon::now();
                $update->switch_type = $getPermissionId;
                $update->reason_for_late = $reason;
                $update->save();

            }

            return redirect()->back()->with('success','Issue Successfully With Your Reason For Late..');

        }catch(\Throwable $th){
            return redirect()->back()->with('error','Internal Server Error!');
        }



    }
    //for issue to design
    public function issueToDesign($id){


        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getDesignRole = Role::where('name','DESIGN/CAM')->first();
            $getPermissionId = Permission::where('name','iss.for.des/cam')->first();

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);


                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getDesignRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }

                $orderdetail->order_status = $getDesignRole->id;
                $orderdetail->save();

                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                  $createNewOrderhistory = Order_history::create([
                     'user_id' =>  Auth::guard('admin')->user()->id,
                     'order_id' => $order_id ,
                     'user_type' => $userType,
                     'typesofwork_id' => $workId,
                     'switch_type' =>  $getPermissionId->id,
                     'issue_time' => Carbon::now(),
                  ]);

                }else{

                     $update = Order_history::find($getOrderhistory->id);
                     $update->issue_time = Carbon::now();
                     $update->switch_type = $getPermissionId->id;
                     $update->save();

                }

                return redirect()->back()->with('success','Issue To Design Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    //for issue to waxing
    public function issueToWaxing($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','WAXING')->first();
            $getPermissionId = Permission::where('name','qc&iss.for.waxing')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type, $getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }

                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();

                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5 || $userType == 2 || $userType == 1) {

                            $update = Order_history::find($getOrderhistory->id);
                            $update->issue_time = Carbon::now();
                            $update->switch_type = $getPermissionId->id;
                            $update->save();
                            $orderdetail->order_status = $getWaxingRole->id;
                            $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }


                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    //for receive for design department
    public function receiveForDesign($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $user_id = Auth::guard('admin')->user()->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.des/cam')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','iss.for.des/cam')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }

            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function receiveForWaxing($id){

        try{

            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.waxing')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','qc&iss.for.waxing')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForCasting($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','CASTING')->first();//
            $getPermissionId = Permission::where('name','qc&iss.for.casting')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                            $update = Order_history::find($getOrderhistory->id);
                            $update->issue_time = Carbon::now();
                            $update->switch_type = $getPermissionId->id;
                            $update->save();
                            $orderdetail->order_status = $getWaxingRole->id;
                            $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }

                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForCasting($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
                               ->where('user_type',$userType)
                               ->where('user_id',Auth::guard('admin')->user()->id)
                               ->first();
            $getPermissionId = Permission::where('name','rec.for.casting')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','qc&iss.for.casting')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForDelivery($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','DELIVERY')->first();//
            $getPermissionId = Permission::where('name','iss.for.delivery')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                            $update = Order_history::find($getOrderhistory->id);
                            $update->issue_time = Carbon::now();
                            $update->switch_type = $getPermissionId->id;
                            $update->save();
                            $orderdetail->order_status = $getWaxingRole->id;
                            $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }
                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForDelivery($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.delivery')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','iss.for.delivery')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForHisab($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','HISAB')->first();//
            $getPermissionId = Permission::where('name','iss.for.hisab')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                }else{

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                            $update = Order_history::find($getOrderhistory->id);
                            $update->issue_time = Carbon::now();
                            $update->switch_type = $getPermissionId->id;
                            $update->save();
                            $orderdetail->order_status = $getWaxingRole->id;
                            $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }

                }
                return redirect()->back()->with('success','Issue To Hisab Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForHisab($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
                               ->where('user_type',$userType)
                               ->where('user_id',Auth::guard('admin')->user()->id)
                               ->first();
            $getPermissionId = Permission::where('name','rec.for.hisab')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','iss.for.hisab')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForCentral($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','CENTRAL')->first();//
            $getPermissionId = Permission::where('name','qc&iss.for.del/cen')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }

                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{

                            // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                            // $getCurrentTime = Carbon::now();
                            // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                                $update = Order_history::find($getOrderhistory->id);
                                $update->issue_time = Carbon::now();
                                $update->switch_type = $getPermissionId->id;
                                $update->save();
                                $orderdetail->order_status = $getWaxingRole->id;
                                $orderdetail->save();

                            // }else {
                            //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                            // }

                  }
                return redirect()->back()->with('success','Issue To Central Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForCentral($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.del/cen')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','qc&iss.for.del/cen')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }
            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForReady($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','READY')->first();//
            $getPermissionId = Permission::where('name','iss.for.ready')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(empty($getOrderhistory)){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{


                            // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                            // $getCurrentTime = Carbon::now();

                            // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                                    $update = Order_history::find($getOrderhistory->id);
                                    $update->issue_time = Carbon::now();
                                    $update->switch_type = $getPermissionId->id;
                                    $update->save();
                                    $orderdetail->order_status = $getWaxingRole->id;
                                    $orderdetail->save();

                            // }else {

                            //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                            // }

                  }
                return redirect()->back()->with('success','Issue To Ready Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForReady($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.ready')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','iss.for.ready')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function issueForPacking($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getWaxingRole = Role::where('name','PACKING')->first();//
            $getPermissionId = Permission::where('name','iss.for.packing')->first();//

            if(isset($orderdetail)){

                $delayCheck = $this->checkDelay($orderdetail->id, Auth::guard('admin')->user()->user_type,$getPermissionId->id);

                if ($delayCheck['delay'] == 'true') {
                    session([
                        'getPermissionId' => $getPermissionId,
                         'orderDetail' => $orderdetail,
                         'getDepartment' => $getWaxingRole,

                    ]);
                    return redirect()->back()->with('massage', $delayCheck['reason']);
                }
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();


                if(!$getOrderhistory){

                    $createNewOrderhistory = Order_history::create([
                       'user_id' =>  Auth::guard('admin')->user()->id,
                       'order_id' => $order_id ,
                       'user_type' => $userType,
                       'typesofwork_id' => $workId,
                       'switch_type' =>  $getPermissionId->id,
                       'issue_time' => Carbon::now(),
                    ]);

                  }else{

                            // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                            // $getCurrentTime = Carbon::now();

                            // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {


                                    $update = Order_history::find($getOrderhistory->id);
                                    $update->issue_time = Carbon::now();
                                    $update->switch_type = $getPermissionId->id;
                                    $update->save();
                                    $orderdetail->order_status = $getWaxingRole->id;
                                    $orderdetail->save();

                            // }else {

                            //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                            // }

                  }
                return redirect()->back()->with('success','Issue To Packing Department Successfully..');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function receiveForPacking($id){

        try{
            $orderdetail = order::where('orderno',$id)->first();
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','rec.for.packing')->first();

            $currentTime = Carbon::now();
            $getPermissionId2 = Permission::where('name','iss.for.packing')->first();
            $getWhenIssue = Order_history::where('order_id',$order_id)->where('switch_type', $getPermissionId2->id)->first();

            $issueTimeToDesign = isset($getWhenIssue->issue_time) ? $getWhenIssue->issue_time :0;

            if ($currentTime->diffInMinutes($issueTimeToDesign) > 30) {
                session([
                    'getPermissionId' => $getPermissionId,
                     'orderDetail' => $orderdetail,

                ]);
               return redirect()->back()->with('massage','lateReceive tall me reason!');
            }
            if(empty($getOrderhistory)){

                $createNewOrderhistory = Order_history::create([
                    'user_id' =>  Auth::guard('admin')->user()->id,
                    'order_id' => $order_id ,
                    'user_type' => $userType,
                    'typesofwork_id' => $orderdetail->counter_id,
                    'receive_switch' =>  $getPermissionId->id,
                    'receive_time' => Carbon::now(),
                 ]);

            }else{

                $update = Order_history::find($getOrderhistory->id);
                $update->receive_time = Carbon::now();
                $update->receive_switch = $getPermissionId->id;
                $update->save();
            }

            return redirect()->back()->with('success','Order Receive successfully at'.now());
        }catch (\Throwable $th) {
            return redirect()->back()->with('error','Order Not Receive, Some Error!');
        }

    }

    public function completeDelivery($id){

      try{
        $orderdetail = order::where('orderno',$id)->first();

        if(isset($orderdetail)){

            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)
            ->where('user_type',$userType)
            ->where('user_id',Auth::guard('admin')->user()->id)
            ->first();
            $getPermissionId = Permission::where('name','delivery/complete')->first();//

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5 || $userType == 9) {

                            $update = Order_history::find($getOrderhistory->id);
                            $update->issue_time = Carbon::now();
                            $update->switch_type = $getPermissionId->id;
                            $update->save();
                            $orderdetail->order_status =  $orderdetail->order_status + 2;//
                            $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }

            return redirect()->back()->with('success','Order Delivery Completed successfully at'.now());
        }
       }catch(\Throwable $th){
        return redirect()->back()->with('error','Order Not Delivery ! Some problem');
       }
    }

    public function issueForSaleing($id){
        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getPermissionId = Permission::where('name','iss.for.saleing')->first();//
            if(isset($orderdetail)){

                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $getOrderhistory= Order_history::where('order_id',$order_id)
                ->where('user_type',$userType)
                ->where('user_id',Auth::guard('admin')->user()->id)
                ->first();

                        // $getReceiveTime = isset($getOrderhistory->receive_time) ? Carbon::parse($getOrderhistory->receive_time) : null;
                        // $getCurrentTime = Carbon::now();

                        // if ($getReceiveTime && $getCurrentTime->diffInMinutes($getReceiveTime) > 5) {

                 $update = Order_history::find($getOrderhistory->id);
                 $update->issue_time = Carbon::now();
                 $update->switch_type = $getPermissionId->id;
                 $update->save();
                 $orderdetail->order_status =  $orderdetail->order_status + 2;//
                 $orderdetail->save();

                        // }else {

                        //     return redirect()->back()->with('error','Issue Button Work After 5 min');
                        // }

                return redirect()->back()->with('success','Order Issued For Saleing successfully at'.now());
            }
           }catch(\Throwable $th){
            return redirect()->back()->with('error','Order Not Issued ! Some problem');
           }
    }


}
