<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use URL;
use Spatie\Permission\Models\{Role}; 
use App\Models\order;
use App\Models\orderimage;
use App\Models\hadleby;
use Exception;
use App\Models\customer_name;
use DataTables;
use App\Models\Order_history;
use App\Models\types_work;
use Carbon\Carbon as CarbonCarbon;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Illuminate\Support\Carbon;
use App\Models\GeneralSetting;
use DateTime;
use DateInterval;
use File;

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

                $selectedValue = $request->input('id');
            $data = types_work::where('id', $selectedValue)->first();
            $maxOrderId = order::max('id') + 1;

            $hour = $data->working_hours;
            $minutes = $data->working_minutes;
            $seconds = $data->working_seconds;

            $timeArray = [$hour, $minutes, $seconds];

            // Implode the array into a string


            // Get the current time
            $currentTime = Carbon::now();

            // Display only the time
            $H = $currentTime->format('H');
            $I = $currentTime->format('i');
            $S = $currentTime->format('s');



            $workingTime = implode(':', $timeArray);
            $totalHours = $hour;
            $startDateTime = Carbon::today()->setHour($H)->setMinute($I)->setSecond($S); // Set the starting time to 9 pm

            $finishDateTime = $this->calculateFinishDateTime($totalHours, $startDateTime);

                if($data == null) {
                    $orderValue = $data;

                } else {

                    $orderValue = $data->order_value . $maxOrderId;

                }

                $qrcodePath = public_path('images/qrcodes/');
                $qrcode_name = null;

                if($data == null) {

                    $qrcode_name = $data;

                } else {

                    $new_shop_url = URL::to('/admin/ordersDetail/') . "/" . $orderValue;
                    $qr_name = $orderValue . "_" . time() . "_qr.svg";
                    $upload_path = public_path('images/qrcodes/' . $qr_name);
                    QrCode::format('svg')->margin(2)->size(200)->generate($new_shop_url, $upload_path);

                    $qrcode_name = $upload_path;
                }


                if ($qrcode_name !== null) {
                    $oldQrcodePath = $qrcodePath . $qrcode_name;
                    if (File::exists($oldQrcodePath)) {
                        File::delete($oldQrcodePath);   
                    }
                }

                if($orderValue != null) {
                    return response()->json(['orderno' => $orderValue, 'qrcode_name' => $qr_name, 'DeliveryDate' => $finishDateTime ]);
                }

               return response()->json(['orderno' => $orderValue]);
        }
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        if($request->ajax()){


            $user_type = Auth::guard('admin')->user()->user_type;

            if($user_type == 1){
                $orders= order::latest();
            }else{
                $orders = order::where('order_status',$user_type)->latest();
            }

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
            

            ->addColumn('actions',function($row){

              $orderNo = isset($row->orderno) ? $row->orderno : '';
              $userdetail =  Auth::guard('admin')->user();
             
              $orderDeletePermission = $userdetail->delete_order;

            //   if($user_type != 1){

            //     $action_html = '<button class="btn rounded-circle btn-sm btn-danger me-1 disabled"><i class="fa fa-eye" aria-hidden="true"></i></a>';  
            //     return $action_html;

            //   }else{
                $action_html = '<div class="row">';
                $action_html .= '<div class="col-md-5">';
                $action_html .= '<a href='.route("order.retrive",["id" => $orderNo ]) .' class="btn btn-sm btn-info rounded-circle"><i class="fa fa-eye" aria-hidden="true"></i></a>';  
                $action_html .= '</div>';
                $action_html .= '<div class="col-md-5">';
                if($orderDeletePermission == 1){
                    $action_html .= '<a onclick="deleteOrderRecord(\''.$orderNo.'\')" class="btn btn-sm btn-danger rounded-circle"><i class="fa fa-trash" aria-hidden="true"></i></a>';   
                }else{
                    $action_html .= '';
                }
                $action_html .= '</div></div>';
                return $action_html;
              //}
            })
            ->rawColumns(['actions','order_status'])
            ->make(true);
        }
       return view('admin.orders.orders');
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
            'counter_id' => $request->counter_id ,
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
       
        $typesofworkId = types_work::where('id',$orderdetail->counter_id)->first();

        $userdetail = Auth::guard('admin')->user();

        try{

            $userId = Auth::guard('admin')->user()->id;
            $department_id = Role::where('id',$userdetail->user_type)->first();
           
           
            $existingScan = Order_history::where('order_id', $orderdetail->id)
            ->where('user_id', $userId)
            ->exists();
            
                $currentDateTime = new DateTime(); // Assuming $currentTime is a DateTime object

                $startDateTime = clone $currentDateTime;
                $startDateTime->setTime(11, 0, 0);

                $endDateTime = clone $currentDateTime;
                $endDateTime->setTime(20, 0, 0);

                $isWithinTimeRange = $currentDateTime > $startDateTime && $currentDateTime < $endDateTime;

            // $currentTime = Carbon::now();
            // $isWithinTimeRange = $currentTime->isAfter($currentTime->setTime(11, 0, 0)) && $currentTime->isBefore($currentTime->setTime(20, 0, 0));
            
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

        return view('admin.orders.orderDetail',compact('orderdetail','orderimages','typesofworkId'));
        
    }

    // public function retrive($id)
    // {
        
    //     $orderdetail = order::where('orderno',$id)->first();
    
    //     $orderimages = orderimage::where('order_id',$orderdetail->id)->get();

       

    //     return view('admin.orders.orderDetail',compact(['orderdetail','orderimages','typesofworkId']));
        
    // }

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

      
    
        return view('admin.orders.orderDetail',compact('orderdetail','orderimages','typesofworkId'));
    }

    //for issue to design
    public function issueToDesign($id){
     
       
        try{
            $orderdetail = order::where('orderno',$id)->first();
            $getDesignRole = Role::where('name','DESIGN/CAM')->first();

            if(isset($orderdetail)){

                $orderdetail->order_status = $getDesignRole->id;
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','iss.for.des/cam')->first();
               
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
                   
                    foreach($getOrderhistory as $value){
                        if($value->user_type == $userType){
                        
                           $value->update([
                               'issue_time'=> Carbon::now(),
                               'switch_type' =>  $getPermissionId->id,
                               ]);
                           $value->save();                  
                        }
                     }
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
            $getWaxingRole = Role::where('name','WAXING')->first();//
            
            if(isset($orderdetail)){
                
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','qc&iss.for.waxing')->first();//
               
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

                      foreach($getOrderhistory as $value){
                       
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update([
                                'issue_time'=> Carbon::now(),
                                'switch_type' => $getPermissionId->id,
                            ]);
                             $value->save();                  
                          }
                       }
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
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.des/cam')->first();
            foreach($getOrderhistory as $value){

                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch'=> $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.waxing')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch' => $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','qc&iss.for.casting')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update([
                                'issue_time'=> now(),
                                'switch_type' =>  $getPermissionId->id,
                            ]);
                             $value->save();                  
                          }
                       }
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.casting')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([  
                        'receive_time'=> Carbon::now(),
                        'receive_switch' => $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','iss.for.delivery')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update([
                                'issue_time'=> Carbon::now(),
                                'switch_type' =>  $getPermissionId->id,
                            ]);
                             $value->save();                  
                          }
                       }
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.delivery')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch' => $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','iss.for.hisab')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update([
                                'issue_time'=> now(),
                                'switch_type' =>  $getPermissionId->id,
                            ]);
                             $value->save();                  
                          }
                       }
                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.hisab')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch'=>$getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','qc&iss.for.del/cen')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update([
                                'issue_time'=> now(),
                                'switch_type' =>  $getPermissionId->id,
                            ]);
                             $value->save();                  
                          }
                       }
                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.del/cen')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch'=> $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','iss.for.ready')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update(['issue_time'=> now(),'switch_type' =>  $getPermissionId->name]);
                             $value->save();                  
                          }
                       }
                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.ready')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){

                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch'=> $getPermissionId->id
                    ]);
                    $value->save();
                }
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
            
            if(isset($orderdetail)){
                $orderdetail->order_status = $getWaxingRole->id;//
                $orderdetail->save();
                $order_id = $orderdetail->id;
                $userType = Auth::guard('admin')->user()->user_type;
                $workId = $orderdetail->counter_id;
                $getOrderhistory= Order_history::where('order_id',$order_id)->get();
                $getPermissionId = Permission::where('name','iss.for.packing')->first();//
               
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
                    
                      foreach($getOrderhistory as $value){
                          if($value->user_type == $userType){
                            // $update= Order_history::find($value->id)->update(['issue_time'=> now()]);
                             $value->update(['issue_time'=> now(),'switch_type' =>  $getPermissionId->id]);
                             $value->save();                  
                          }
                       }
                  }
                return redirect()->back()->with('success','Issue To Waxing Department Successfully..');
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
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','rec.for.packing')->first();
            foreach($getOrderhistory as $value){
                if($value->user_type == $userType){
                    $value->update([
                        'receive_time'=> Carbon::now(),
                        'receive_switch'=>$getPermissionId->id
                    ]);
                    $value->save();
                }
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
            $orderdetail->order_status =  $orderdetail->order_status + 2;//
            $orderdetail->save();
        }
        $order_id = $orderdetail->id;
        $userType = Auth::guard('admin')->user()->user_type;
        $getOrderhistory= Order_history::where('order_id',$order_id)->get();
        $getPermissionId = Permission::where('name','delivery/complete')->first();//
        foreach($getOrderhistory as $value){
          
            if($value->user_type == $userType){
            
                $value->update([
                    'issue_time'=> Carbon::now(),
                    'switch_type' => $getPermissionId->id,
                ]);
              
            }
        }
        return redirect()->back()->with('success','Order Delivery Completed successfully at'.now()); 

       }catch(\Throwable $th){
        return redirect()->back()->with('error','Order Not Delivery ! Some problem');
       }
    }
    
    public function issueForSaleing($id){
        try{
            $orderdetail = order::where('orderno',$id)->first();
            if(isset($orderdetail)){
                $orderdetail->order_status =  $orderdetail->order_status + 2;//
                $orderdetail->save();
            }
            $order_id = $orderdetail->id;
            $userType = Auth::guard('admin')->user()->user_type;
            $getOrderhistory= Order_history::where('order_id',$order_id)->get();
            $getPermissionId = Permission::where('name','iss.for.saleing')->first();//
            foreach($getOrderhistory as $value){
              
                if($value->user_type == $userType){
                
                    $value->update([
                        'issue_time'=> Carbon::now(),
                        'switch_type' => $getPermissionId->id,
                    ]);
                  
                }
            }
            return redirect()->back()->with('success','Order Issued For Saleing successfully at'.now()); 
    
           }catch(\Throwable $th){
            return redirect()->back()->with('error','Order Not Issued ! Some problem');
           }
    }


}
