@php
   $user_dt = Auth::guard('admin')->user();
   $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Block Orders - Order Management System')
@section('content')

<div class="pagetitle">
        <h1>Blocked Orders List</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Blocked Orders</li>
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
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="OrderTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Order No.</th>
                                        <th scope="col">Customer</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col">Order Type</th>
                                        <th scope="col">Block Reason</th>
                                        <th scope="col">Handle By</th>
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
                url: "{{ route('orders.blocklist') }}",
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
                    data:'block_reason',
                    name:'block_reason'
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
                },
            ]
        });

    });

</script>

@endsection
