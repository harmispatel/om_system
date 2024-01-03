@php


   $counterId= $orderdetail->counter_id;
   $counterRole = App\Models\Role::first();
   $counterName = App\Models\Role::where('id', $counterId )->first();
   $user_dt = Auth::guard('admin')->user();
    $role_id = $user_dt->user_type;
    $permissions = App\Models\RoleHasPermissions::where('role_id', $role_id)->pluck('permission_id');

    foreach ($permissions as $permission) {
    $permission_ids[] = $permission;
    }
    $counter_permission = Spatie\Permission\Models\Permission::where('name', 'new_order')->first();
    $counter1_permission = Spatie\Permission\Models\Permission::where('name', 'repeat_order')->first();

    $design_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.des/cam')->first();
    $design1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.des/cam')->first();
    $waxing_permission = Spatie\Permission\Models\Permission::where('name', 'qc&iss.for.waxing')->first();
    $waxing1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.waxing')->first();
    $casting_permission = Spatie\Permission\Models\Permission::where('name', 'qc&iss.for.casting')->first();
    $casting1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.casting')->first();
    $hisab_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.hisab')->first();
    $hisab1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.hisab')->first();
    $central_permission = Spatie\Permission\Models\Permission::where('name', 'qc&iss.for.del/cen')->first();
    $central1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.del/cen')->first();
    $issReady_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.ready')->first();
    $recReady_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.ready')->first();
    $del_or_complete = Spatie\Permission\Models\Permission::where('name', 'delivery/complete')->first();
    $delivery_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.delivery')->first();
    $delivery1_permission= Spatie\Permission\Models\Permission::where('name', 'rec.for.delivery')->first();
    $packing_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.packing')->first();
    $packing1_permission = Spatie\Permission\Models\Permission::where('name', 'rec.for.packing')->first();
    $saleing_permission = Spatie\Permission\Models\Permission::where('name', 'iss.for.saleing')->first();

  $getOrderhistory = App\Models\Order_history::where('user_type',$role_id)->get();
    foreach($getOrderhistory as $oneRecord){
        if($orderdetail->id == $oneRecord->order_id)
        {
            $oneRecordDate = $oneRecord->receive_time;
        }
    }
    $getReceiveDate = isset($oneRecordDate) ? $oneRecordDate : '';
    $getPermissionIdValue = session('getPermissionId', null);
    $getOrderDetail = session('orderDetail',null);
    $orderId = $getOrderDetail ? $getOrderDetail->id:'';
    $getDepartmentDetail = session('getDepartment',null);
    $PermissionType = $getPermissionIdValue ? $getPermissionIdValue->permission_type :'';
    $departmentId = $getDepartmentDetail ? $getDepartmentDetail->id : '';
    $issueSwitch = $getPermissionIdValue ? $getPermissionIdValue->name : '';
    $issueSwitchId = $getPermissionIdValue ? $getPermissionIdValue->id : '';

@endphp
@extends('admin.layouts.admin-layout')

@section('title', 'Order-Detail')

@section('content')

<!-- bootstrap 5.2 model -->
<div class="modal" tabindex="-1" id="mymodel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span style="color:red;">Why Getting Late ?</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ $PermissionType == 1 ? route('late-receive') : route('late-issue') }}" method="POST" id="lateIssueForm">
            @csrf
            <div class="col-md-12 mb-3">
                    <div class="form-label">Switch Type : {{$issueSwitch}}
                        <input type="hidden" name="permission_id" value="{{$issueSwitchId}}">
                        <input type="hidden" name="order_id" value="{{ $orderId }}">
                        <input type="hidden" name="department_id" value="{{$departmentId}}">
                    </div>
                    <label class="form-label">Reasons <span class="text-danger">*</span></label><br>
                    <div style="border: 1px solid {{ ($errors->has('switch1')) ? 'red' : '#ced4da' }}; padding: 10px; border-radius: 0.375rem">
                        @if (count($reasons) > 0)
                            @foreach ($reasons as $switch)
                                <div class="mb-1">
                                    <input type="radio" name="switch1_option" id="switch1_{{ $switch['id'] }}" value="{{ $switch['reason'] }}">
                                    <label for="switch1_{{ $switch['id'] }}" style="cursor: pointer; font-weight:700; font-size:15px;">{{ $switch['reason'] }}</label>
                                </div>
                                <hr style="margin: 0.5rem 0">
                            @endforeach
                            <div class="mb-1">
                                <input type="radio" name="switch1_option" id="switch1_other" value="">
                                <label for="switch1_other" style="cursor: pointer; font-weight:700; font-size:15px;">Other</label>
                                <div id="otherReasonContainer" style="display:none;">
                                    <input type="text" name="switch1_text" class="form-control" id="otherReasonInput" placeholder="Enter your reason">
                                </div>
                            </div>
                        @endif
                    </div>
                    @if ($errors->has('switch1'))
                        <div class="text-danger" style="margin-top: 0.25rem;">
                            {{ $errors->first('switch1') }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($PermissionType == 1)
                    <button id="submitIssue"type="submit" class="btn btn-warning">Receive</button>
                @else
                    <button id="submitIssue"type="submit" class="btn btn-warning">Issue</button>
                @endif
            </div>
      </form>
    </div>
  </div>
</div>



<!-- <span class="text-start"><a href="{{ route('order') }}" class="btn btn-sm btn-primary">Back</a></span> -->

<section class="section dashbord">
    <!-- Image and text -->
    <div class="row">

        <div class="col-md-12">
            <div class="card m-2 p-2">
                <div class="">

                        @if($user_dt->user_type == $orderdetail->order_status)

                            @if(in_array($counter_permission->id,$permission_ids))
                                <a href="{{route('orders.create')}}" class="btn btn-outline-info text-dark" title="New Order">New Order</a>
                            @endif

                            @if(in_array($design_permission->id,$permission_ids))
                                <a href="{{route('orders.issue.design',$orderdetail->orderno) }}" class="btn btn-outline-info text-dark issue" title="Issue To Design">Issue To Design</a>
                            @endif


                            @if(empty($getReceiveDate))

                                @if(in_array($design1_permission->id,$permission_ids) )
                                    <a href="{{route('orders.rec.design',$orderdetail->orderno) }}" class="btn btn-outline-info text-dark" title="Receive For Design">Receive For Design</a>
                                @endif
                            @else
                                @if(in_array($design1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Design</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null || $role_id == 2 || $role_id == 1)
                                @if(in_array($waxing_permission->id,$permission_ids))
                                    <a href="{{route('orders.issue.waxing', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Issue To Waxing">Issue To Waxing</a>
                                @endif
                            @else
                                @if(in_array($waxing_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Waxing</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($waxing1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.waxing', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Receive For Waxing">Receive For Waxing</a>
                                @endif
                            @else
                                @if(in_array($waxing1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Waxing</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($casting_permission->id,$permission_ids))
                                <a href="{{route('orders.iss.casting', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Issue To Casting">Issue To Casting</a>
                                @endif
                            @else
                                @if(in_array($casting_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Casting</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($casting1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.casting', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Receive For Casting">Receive For Casting</a>
                                @endif
                            @else
                                @if(in_array($casting1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Casting</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($hisab_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.hisab', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Issue To Hisab">Issue To Hisab</a>
                                @endif
                            @else
                                @if(in_array($hisab_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Hisab</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($hisab1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.hisab', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Receive For Hisab</a>
                                @endif
                            @else
                                @if(in_array($hisab1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Hisab</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($central_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.central', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Repeat Order">Issue To Central</a>
                                @endif
                            @else
                                @if(in_array($central_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Central</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($central1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.central', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Receive For Central</a>
                                @endif
                            @else
                                @if(in_array($central1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Central</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($issReady_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.ready', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Repeat Order">Issue To Ready</a>
                                @endif
                            @else
                                @if(in_array($issReady_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Ready</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($recReady_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.ready', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Receive For Ready</a>
                                @endif
                            @else
                                @if(in_array($recReady_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Ready</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($del_or_complete->id,$permission_ids))
                                    <a href="{{route('orders.delivery', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Delivery To Customer</a>
                                @endif
                            @else
                                @if(in_array($del_or_complete->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Delivery To Customer</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($delivery1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.delivery', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Receive for delivery</a>
                                @endif
                            @else
                                @if(in_array($delivery1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Delivery</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($delivery_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.delivery', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Repeat Order">Issue To Delivery</a>
                                @endif
                            @else
                                @if(in_array($delivery_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Delivery</button>
                                @endif
                            @endif

                            @if(empty($getReceiveDate))
                                @if(in_array($packing1_permission->id,$permission_ids))
                                    <a href="{{route('orders.rec.packing', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark" title="Repeat Order">Receive For Packing</a>
                                @endif
                            @else
                                @if(in_array($packing1_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Receive For Packing</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($saleing_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.saleing', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Repeat Order">Issue To Saleing</a>
                                @endif
                            @else
                                @if(in_array($saleing_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Saleing</button>
                                @endif
                            @endif

                            @if($getReceiveDate != null)
                                @if(in_array($packing_permission->id,$permission_ids))
                                    <a href="{{route('orders.iss.packing', $orderdetail->orderno)}}" class="btn btn-outline-info text-dark issue" title="Repeat Order">Issue To Packing</a>
                                @endif
                            @else
                                @if(in_array($packing_permission->id,$permission_ids))
                                    <button class="btn btn-light text-secondary" title="Issue To Waxing" disabled>Issue To Packing</button>
                                @endif
                            @endif

                        @else
                        @endif
                </div>


                <div class="text-end m-2" >


                    @if($role_id == 2 || $role_id == 1)
                         <a href="{{route('order.print',$orderdetail->orderno)}}" target="_blank" id="printButton" class="btn btn-sm btn-warning rounded-circle" text="end" title="Print"><i
                                class="fa fa-print" aria-hidden="true"></i></a>
                        <button onClick="share()" class="btn btn-sm btn-primary rounded-circle" title="Share"><i
                                class="fa fa-share-alt" aria-hidden="true"></i></button>
                    @endif

                </div>

                <div class="container border p-2" >
                    <!-- <h2 class="text-center"><span><img src="{{asset('public/images/demo_images/logos/logo.png')}}"
                                height="50px" width="70px"></span></h2> -->

                    <div class="row">

                        <div class="">
                            <img alt="image"
                                 src="{{ asset('public/images/qrcodes/'. $orderdetail->Qrphoto)}}"
                             class="">
                        </div>
                        <div class="col-lg-12 col-md-12 text-center">
                            <h5 class="card-title"> Customer Order </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <table class="table">
                                <tr>
                                    <td>Order</td>

                                    <td> {{
                                        $orderdetail->SelectOrder == 0
                                            ? "New Order"
                                            : "Repeat Order"
                                    }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Counter Name</td>

                                    <td> @if(isset($orderdetail->counter_id))
                                        {{$typesofworkId->types_of_works}}
                                        @else
                                        <p>-</p>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Order No</td>
                                    <td>{{ $orderdetail->orderno }}</td>
                                </tr>
                                <tr>
                                    <td>Name</td>
                                    <td> {{ $orderdetail->name }} </td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td> {{ $orderdetail->mobile }}</td>
                                </tr>
                                <tr>
                                    <td>Who's Metal</td>
                                    <td> {{ $orderdetail->gold }}</td>
                                </tr>
                                <tr>
                                    <td>Metal</td>
                                    <td>{{$orderdetail->metal}}</td>
                                </tr>

                            </table>
                        </div>




                        <div class="col-lg-6">
                            <table class="table">

                                <tr>
                                    <td>Touch</td>
                                    <td> {{   number_format($orderdetail->touch,2) }}</td>
                                </tr>
                                <tr>
                                    <td>Charges</td>
                                    <td>{{ $orderdetail->charges }}</td>
                                </tr>
                                <tr>
                                    <td>Advance</td>
                                    <td>{{ $orderdetail->advance }}</td>
                                </tr>
                                <tr>
                                    <td>Metal Weight</td>
                                    <td>{{ $orderdetail->metalwt }}</td>
                                </tr>
                                <tr>
                                    <td>Delivery Date</td>
                                    <td>{{ $orderdetail->deliverydate }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 p-4">
                            <table class="table border border-info table-light mt-5">
                                <!-- <thead>
                                    <tr>
                                        <th colspan="2" class="text-center">Title</th>
                                    </tr>
                                </thead> -->
                                <tbody class="text-center">
                                    <tr>
                                        <td scope="row">
                                            @if(isset($orderimages))
                                            @foreach ($orderimages as $value)

                                            <img alt="image" class="w-50"
                                                src="{{ asset('public/orderimages/'. $value->orderimage)}}">

                                            @endforeach
                                            @else

                                            <img src="{{asset('public/images/demo_images/not-found/not-found4.png')}}"
                                                height="600px" width="450px">
                                            @endif
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row m-3 p-4">
                            <div class="col-lg-12 text-end">
                                <label class="fw-bold">Handle By : <span class="fw-bold">{{ $orderdetail->handleby
                                        }}</span></label>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>


@endsection





{{-- Custom JS --}}
@section('page-js')

<script type="text/javascript">
$(document).ready(function() {

    // Check if there's a message in the session
    let errorMessage = "{{ session('massage') }}";
    if (errorMessage) {
        // Show the modal with the error message
        $('#mymodel').modal('show');
    }
});

$(document).ready(function() {
    // Initially, disable the "Issue" button
    $("#submitIssue").prop('disabled', true);

    // Check if any radio button with the name 'switch1' is selected
    $('input[name="switch1_option"]').on('change', function() {

        if ($('input[name="switch1_option"]:checked').length > 0) {
            // Enable the "Issue" button
            $("#submitIssue").prop('disabled', false);

              // If "Other" option is selected, show the input field
              if ($('input[name="switch1_option"]:checked').val() === '') {
                $("#submitIssue").prop('disabled', true);
                $("#otherReasonContainer").show();

              } else {
                  $("#otherReasonContainer").hide();
              }
        } else {
            // Disable the "Issue" button
            $("#submitIssue").prop('disabled', true);
        }
    });

});

$('#otherReasonInput').on('keyup',function(){
    var inp = $(this).val();
    if(inp.length > 0){
        $("#submitIssue").prop('disabled', false);
    }else{
        $("#submitIssue").prop('disabled', true);
    }
});

function share(){
        var currenturl = window.location.href;
        if(navigator.share){
            navigator.share({
            title : 'Your shared Title',
            url : '{{route("order.show.print",$orderdetail->orderno)}}',
            })
            .then(()=> console.log('Successful share'))
            .catch(error => console.log('Error sharing: ',error));
        }else{
            console.log('web share api not supported.');
        }
    }


    document.getElementById('printButton').addEventListener('click', function() {
            var printContent = document.getElementById('printableArea');
            var originalContents = document.body.innerHTML;

            // Clone the printable content to a new document for printing
            var printWindow = window.open('', '_self');
            printWindow.document.write(printContent.innerHTML);
            printWindow.document.close();

            // Wait for the new document to be ready before printing
            printWindow.onload = function() {
                printWindow.print();
                printWindow.onafterprint = function() {
                    // Clean up after printing
                    printWindow.close();
                };
            };
        });



toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    timeOut: 10000
}

@if(Session::has('success'))
toastr.success('{{ Session::get('success') }}')
@endif


@if(Session::has('lateReceive'))
   toastr.error('{{Session::get('lateReceive')}}')
@endif
// @if (Session::has('error'))
//     toastr.error('{{ Session::get('error') }}')
// @endif

</script>

@endsection
