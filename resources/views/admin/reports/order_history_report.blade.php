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
@section('title', 'Order Management System | Reports | Order History')
@section('content')

    {{--page title--}}
    <div class="pagetitle">
        <h1>Reports</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Order History</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <a href="{{ route('reports.trassed') }}" class="btn btn-sm bg-danger">
                <i class="fab fa-google-drive text-light"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Order History Report Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                           <div class="row">
                                <div class="col-md-5">
                                    <label class="me-2">Order No.</label>
                                    <input type="text" id="order_number" name="order_number" class="rounded me-2 form-control" placeholder="Enter Order Number to Search Your Order">
                                </div>
                                <div class="col-md-1">
                                    <button id="searchBtn" class="btn btn-dark mt-4"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </div>
                           </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="OrderHistoryReportTable">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Customer Contact-No</th>
                                        <th style="width: 25%">Customer</th>
                                        <th>Current Department</th>
                                        <th style="width: 15%">Duration</th>
                                        <th>Handle By</th>
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

@section('page-js')
    <script type="text/javascript">

        $(function() {

            // Get Orders History Reports
            var table = $('#OrderHistoryReportTable').DataTable({
                lengthChange: false,
                paging:true,
                // searching: false,
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url:"{{ route('reports.order_history') }}",
                    data: function(d) {
                        d.order_number = $('#order_number').val(); // Pass the order number to the server
                    }
                },
                columns: [
                    {
                        data: 'orderno',
                        name: 'orderno'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'current_department',
                        name: 'current_department',
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

            // Search Specific Order
            $('#searchBtn').on('click', function(){
                table.ajax.reload(); // Redraw the DataTable with the new filter
            });

        });

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
@endsection
