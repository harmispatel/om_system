@php
   $user_dt = Auth::guard('admin')->user();
   $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Orders - Order Management System')
@section('content')
<!-- bootstrap 5.2 model -->
<div class="modal" tabindex="-1" id="mymodel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span style="color:red;">Why Block This Order ?</span></h5>
        <!-- <h5 class="modal-title">Confirm Block Order ID: <span id="orderId"></span></h5> -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('order.block') }}" method="POST">
            @csrf
            <input type="hidden" id="orderId" name="orderId">
            <div class="col-md-12 mb-3">
                <!-- <label for="block_reason" class="form-label">Select Reason*</label>
                <textarea class="form-control" id="block_reason" rows="3"></textarea> -->
                <label class="form-label">Reasons <span class="text-danger">*</span></label><br>
                    <div style="border: 1px solid {{ ($errors->has('switch1')) ? 'red' : '#ced4da' }}; padding: 10px; border-radius: 0.375rem">
                        @if (count($block_reasons) > 0)
                            @foreach ($block_reasons as $switch)
                                <div class="mb-1">
                                    <input type="radio" name="block_reason" id="block_reason" value="{{ $switch['reason'] }}">
                                    <label for="block_reason" style="cursor: pointer; font-weight:700; font-size:15px;">{{ $switch['reason'] }}</label>
                                </div>
                                <hr style="margin: 0.5rem 0">
                            @endforeach
                            <div class="mb-1">
                                <input type="radio" name="block_reason" id="block_reason_other" value="">
                                <label for="block_reason_other" style="cursor: pointer; font-weight:700; font-size:15px;">Other</label>
                                <div id="otherReasonContainer" style="display:none;">
                                    <input type="text" name="block_reason_other" class="form-control" id="otherReasonInput" placeholder="Enter your reason">
                                </div>
                            </div>
                        @endif
                    </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="submitIssue"type="submit" class="btn btn-warning">Submit</button>
            </div>
      </form>
    </div>
  </div>
</div>
    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Orders</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Orders</li>
                    </ol>
                </nav>
            </div>
            @if($user_type == 1 || $user_type == 2)
                <div class="col-md-4" style="text-align: right;">
                    <a href="{{ route('orders.create') }}" class="btn btn-sm custom-btn">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Orders Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <div class="card-title">
                        <div class="row">
                             <div class="col-md-4">
                                 <label for="form-control">Start Date</label>
                                 <input type="date" name="startdate" id = "startdate" class="form-control" placeholder="start Date" value="{{\Carbon\Carbon::parse($firstDayOfCurrentMonth)->toDateString()}}">
                            </div>
                             <div class="col-md-4">
                                 <label for="form-control">End Date</label>
                                 <input type="date" name="enddate" id = "enddate" class="form-control" placeholder="End Date" value="{{\Carbon\Carbon::parse($lastDayOfLastMonth)->toDateString()}}">
                             </div>
                             <div class="col-md-1">
                                <button id="search" class="btn custom-btn mt-4"><i class="fa fa-search"></i></i></button>
                             </div>
                        </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="OrderTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Order No.</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Order Type</th>
                                        <th scope="col">Touch</th>
                                        <th scope="col">Who's Metal</th>
                                        <th scope="col">Metal</th>
                                        <th scope="col">Delivery Date</th>
                                        <th scope="col">Handle By</th>
                                        <th scope="col">Order Status</th>
                                        <th scope="col">Created Date</th>
                                        <!-- <th scope="col">Block Status</th> -->
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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

    // Load All Orders
    $(function() {
        var table = $('#OrderTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 100,
            ajax:{
                url: "{{ route('order') }}",
                data:function(d){
                    d.startDate = $('#startdate').val();
                    d.endDate= $('#enddate').val();
                }
            },
            columns: [
                {
                    data: 'orderno',
                    name: 'orderno'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    data: 'SelectOrder',
                    name: 'SelectOrder',
                },
                {
                    data: 'touch',
                    name: 'touch',
                    render: function(data, type, row) {
                        return parseFloat(data).toFixed(2);
                    }
                },
                {
                    data: 'gold',
                    name: 'gold'
                },
                {
                    data: 'metal',
                    name: 'metal'
                },
                {
                    data: 'deliverydate',
                    name: 'deliverydate',
                },
                {
                    data: 'handleby',
                    name: 'handleby',
                },
                {
                    data: 'order_status',
                    name: 'order_status',
                    orderable: false,
                    searchable: false
                },
                {
                    data:'created_date',
                    name:'created_date'
                },
                // {
                //     data:'block',
                //     name:'block'
                // },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $('#search').on('click',function(){
            table.ajax.reload();
        });


        $('#lateIssueForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
            const blockReason = $('#block_reason').val();
            const order_id = $('#orderId').val();

            $.ajax({
                type: "POST",
                url: "{{ route('order.block') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "blockReason": blockReason,
                    "order_id":order_id,
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.success == 1) {
                        toastr.success(response.message);
                        $('#mymodel').hide();
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message);
                        $('#mymodel').hide();
                    }
                }
            });
        });

    });

$(document).ready(function() {
    // Initially, disable the "Issue" button
    $("#submitIssue").prop('disabled', true);

    // Check if any radio button with the name 'switch1' is selected
    $('input[name="block_reason"]').on('change', function() {

        if ($('input[name="block_reason"]:checked').length > 0) {
            // Enable the "Issue" button
            $("#submitIssue").prop('disabled', false);
            if ($('input[name="block_reason"]:checked').val() === '') {

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
     // Function for change block of User
    function BlockOrder(is_bloked, id) {

        $.ajax({
            type: "POST",
            url: "{{ route('order.block') }}",
            data: {
                "_token": "{{ csrf_token() }}",
                "is_bloked": is_bloked,
                "id": id
            },
            dataType: 'JSON',
            success: function(response) {
                if (response.success == 1) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            }
        })
    }
    // Function for Delete Record
    function blockOrderRecord(id){
        swal({
            title: "Are You Sure Want to Block It?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willDeleteRole) => {
            if (willDeleteRole) {
                $('#orderId').val(id);
                $('#mymodel').show();
            } else {
                swal("Cancelled", "", "error");

            }
        });
    }

    @if(Session::has('success'))
        toastr.success("{{ Session::get("success") }}")
    @endif

</script>

@endsection
