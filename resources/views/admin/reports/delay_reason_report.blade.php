@php
   $user_dt = Auth::guard('admin')->user();
   $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Delay Reason Report - Order Management System')
@section('content')

<div class="pagetitle">
        <h1>Delay Reason Report</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Delay Reason Report</li>
                    </ol>
                </nav>
            </div>
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
                            <label class="text-center">Delay-Reason Report In Issue</label>
                                    <div class="col-md-6 mt-2">
                                        {{-- <label for="role">Select Department:</label> --}}
                                        <select id="department" name="department" class="form-select">
                                            <option value="0">All Departments</option>
                                            @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>

                            </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="delayReasonTable">
                                <thead>
                                    <tr>
                                        <th>Index</th>
                                        <th>Order No</th>
                                        <th>Late Issue Switch</th>
                                        <th>Reason For Late</th>
                                        <th>Who's Late Order</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                            <label class="text-center">Delay-Reason Report In Receive</label>
                                    <div class="col-md-6 mt-2">
                                        {{-- <label for="role">Select Department:</label> --}}
                                        <select id="department2" name="department" class="form-select">
                                            <option value="0">All Departments</option>
                                            @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>

                            </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="delayReason2Table">
                                <thead>
                                    <tr>
                                        <th>Index</th>
                                        <th>Order No</th>
                                        <th>Late Receive Switch</th>
                                        <th>Reason For Late</th>
                                        <th>Who's Late Order</th>
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
    var table = $('#delayReasonTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url:"{{ route('reports.delayreason') }}",
            data:function(d){
                d.department = $('#department').val();

            }
        },
        columns: [
            { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
            {
                data: 'order_no',
                name: 'order_no'
            },
            {
                data:'switch_name',
                name:'switch_name'
            },
            {
                data:'reason_for_late',
                name:'reason_for_late'
            },
            {
                data: 'whos_late',
                name: 'whos_late'
            },
        ]
        });

        $('#department').on('change', function() {
                table.ajax.reload(); // Redraw the DataTable with the new filter
        });

});

$(function() {
    var table = $('#delayReason2Table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url:"{{ route('reports.delayreceivereason') }}",
            data:function(d){
                d.department = $('#department2').val();

            }
        },
        columns: [
            { data: 'DT_RowIndex', 'orderable': false, 'searchable': false },
            {
                data: 'order_no',
                name: 'order_no'
            },
            {
                data:'switch_name',
                name:'switch_name'
            },
            {
                data:'late_receive_reason',
                name:'late_receive_reason'
            },
            {
                data: 'whos_late',
                name: 'whos_late'
            },
        ]
        });

        $('#department2').on('change', function() {
                table.ajax.reload(); // Redraw the DataTable with the new filter
        });

});
</script>
@endsection
