@php
$user_dt = Auth::guard('admin')->user();
$role_id = $user_dt->user_type;
$permissions = App\Models\RoleHasPermissions::where('role_id', $role_id)->pluck('permission_id');

foreach ($permissions as $permission) {
$permission_ids[] = $permission;
}

$report_permission = Spatie\Permission\Models\Permission::where('name', 'reports')->first();
@endphp
@extends('admin.layouts.admin-layout')
@section('content')

{{--page title--}}
<div class="pagetitle">
    <h1>Report</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Order-history Report</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- section for Report --}}
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
                        <label>Order No</label>
                        <input type="text" id="order_number" name="order_number" class="rounded w-50">
                        <button id="searchBtn" class="btn btn-dark btn-sm">Search</button>
                    </div>
                    <div class="table-responsive custom_dt_table">
                        <table class="table w-100" id="OrderHistoryTable">
                            <thead>
                                <tr>
                                    <!--<th>Serial No</th>-->
                                    <th>Order No</th>
                                    <th>Customer Name</th>
                                    <th>Current Department</th>
                                    <th>Duration</th>
                                    <th>Handle By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody style="text-align:center;"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('page-js')

<script type="text/javascript">

$(function() {

var table = $('#OrderHistoryTable').DataTable({
    lengthChange: false, 
    paging:false,
    searching: false,
    processing: true,
    serverSide: true,
    pageLength: 100,
    ajax: {
        url:"{{ route('reports.orderhistory') }}",
        data: function(d) {
           d.order_number = $('#order_number').val(); // Pass the order number to the server
        }
    },
    columns: [
        // {
        //     data: 'id',
        //     name: 'id'
        // },
        {
            data: 'orderno',
            name: 'orderno'
        },
        {
            data: 'name',
            name: 'name',
        },
        {
            data: 'current_department',
            name: 'current_department'
        },
        {
            data: 'duration',
            name: 'duration',
        },
        {
            data: 'handleby',
            name: 'handleby',
        },
        {
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false
            
        }
    ]
});

            $('#searchBtn').on('click', function() {
                table.ajax.reload(); // Redraw the DataTable with the new filter
            });
});


        toastr.options =
        {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            timeOut: 10000
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        // @if (Session::has('error'))
        //     toastr.error('{{ Session::get('error') }}')
        // @endif
   
</script>

@endsection