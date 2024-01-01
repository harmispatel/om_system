@extends('admin.layouts.admin-layout')

@section('title', 'Orders')

@section('content')
@php

   $user_dt = Auth::guard('admin')->user();
   $role_id = $user_dt->user_type;
   $permissions = App\Models\RoleHasPermissions::where('role_id', $role_id)->pluck('permission_id');

    foreach ($permissions as $permission) {
    $permission_ids[] = $permission;
    }

    $counter_permission = Spatie\Permission\Models\Permission::where('name', 'new_order')->first();
    $counter1_permission = Spatie\Permission\Models\Permission::where('name', 'repeat_order')->first();
@endphp
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
        <div class="col-md-4" style="text-align: right;">
        @if(in_array($counter_permission->id,$permission_ids))
            <a href="{{ route('orders.create') }}" class="btn btn-sm custom-btn">
                <i class="bi bi-plus-lg"></i>
            </a>
        @endif
        </div>
    </div>
</div>

{{-- Orders Section --}}
<section class="section dashboard">
    <div class="row">
        {{-- Errors Message --}}
        @if (session()->has('errors'))
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('errors') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    <div class="row">

        {{-- Order Listing Card --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                    </div>
                    <div class="table-responsive custom_dt_table">
                        <table class="table w-100" id="OrderTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Order No</th>
                                    <th>Customer Name</th>
                                    <th>Mobile-No</th>
                                    <th>Order Type</th>
                                    <th>Touch</th>
                                    <th>Who's Metal</th>
                                    <th>Metal</th>
                                    <!--<th>Order QrCode</th>-->
                                    <th>Delivery Date</th>
                                    <th>Handle By</th>
                                    <th>Order Status</th>
                                    <th>Actions</th>
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

$(function() {

var table = $('#OrderTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 100,
    ajax: "{{ route('order') }}",
    columns: [{
            data: 'id',
            name: 'id'
        },
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
                    // Format the value with two decimal places
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
        // {
        //     data:'Qrphoto',
        //     name:'Qrphoto',
        //     render: function( data, type, full, meta ) {
        //         if(data == null){
        //           return "";
        //         }else{
        //             return "<img src=\"{{asset('/public/images/qrcodes/')}}"+"/"+data+"\" height=\"50\"/>";
        //         }

        //     }
        // },
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
        },
        {
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false
        },
    ]
});

});

function deleteOrderRecord(orderNo) {

swal({
        title: "Are you sure You want to Delete It ?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDeleteRole) => {
        if (willDeleteRole) {
            $.ajax({
                type: "POST",
                url: "{{ route('order.destroy') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'id': orderNo,
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.success == 1) {
                        toastr.success(response.message);
                        $('#OrderTable').DataTable().ajax.reload();
                    } else {
                        swal(response.message, "", "error");
                    }
                }
            });
        } else {
            swal("Cancelled", "", "error");

        }
    });
}

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    timeOut: 10000
}

@if(Session::has('success'))
    toastr.success("{{ Session::get("success") }}")
@endif

@if (Session::has('error'))
    toastr.error('{{ Session::get('error') }}')
@endif
</script>

@endsection
