@php
   $user_dt = Auth::guard('admin')->user();
   $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Orders - Order Management System')
@section('content')

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
                             <div class="col-md-2">
                                 <label for="form-control">Start Date</label>
                                 <input type="date" name="startdate" id = "startdate" class="form-control" placeholder="start Date">
                            </div>
                             <div class="col-md-2">
                                 <label for="form-control">End Date</label>
                                 <input type="date" name="enddate" id = "enddate" class="form-control" placeholder="End Date">
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
                },
                {
                    data:'created_date',
                    name:'created_date'
                },
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
    });

    // Function for Delete Record
    function deleteOrderRecord(orderNo){
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

    @if(Session::has('success'))
        toastr.success("{{ Session::get("success") }}")
    @endif

</script>
@endsection
