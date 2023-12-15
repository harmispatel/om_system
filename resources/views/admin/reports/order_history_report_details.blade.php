@extends('admin.layouts.admin-layout')
@section('title', 'Order Management System | Reports | Order History | Details')
@section('content')

    {{-- page title --}}
    <div class="pagetitle">
        <h1>Reports</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.order_history') }}">Order History Report</a></li>
                        <li class="breadcrumb-item active">Details</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Order History Report Details Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="OrderHistoryReportTable">
                                <thead>
                                    <tr>
                                        <th>Order No.</th>
                                        <th>Switch In & Time</th>
                                        <th>Switch Out & Time</th>
                                        <th>User Name</th>
                                        <th>Duration</th>
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
            // Get Orders History Report Details
            var table = $('#OrderHistoryReportTable').DataTable({
                lengthChange: false,
                paging: false,
                searching: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url:"{{ route('reports.order_history_details',$id) }}",
                },
                columns: [
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data: 'inswitch_time',
                        name: 'inswitch_time',
                    },
                    {
                        data: 'outswitch_time',
                        name: 'outswitch_time'
                    },

                    {
                        data: 'user_name',
                        name: 'user_name'
                    },

                    {
                        data: 'duration',
                        name: 'duration',
                    },
                ]
            });
        });

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
@endsection
