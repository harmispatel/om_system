@php
   $user_dt = Auth::guard('admin')->user();
   $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Block Reason - Order Management System')
@section('content')

<div class="pagetitle">
        <h1>List Of Reasons</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">List Of Reasons</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-md-12" style="text-align: right;">

            <a href="{{ route('block-reasons.create') }}" class="btn btn-sm custom-btn">
                <i class="bi bi-plus-lg"></i>
            </a>

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
                                        <th scope="col">Id No.</th>
                                        <th scope="col">Reasons For Block-Order</th>
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
                url: "{{ route('block-reasons') }}",
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'reason',
                    name: 'reason'
                },
            ]
        });

    });

</script>

@endsection
