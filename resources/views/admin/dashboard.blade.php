@extends('admin.layouts.admin-layout')

@section('title', 'Dashboard')

@section('content')

@php
    $user_dt = Auth::guard('admin')->user();
    $role_id = $user_dt->user_type;
    $permissions = App\Models\RoleHasPermissions::where('role_id', $role_id)->pluck('permission_id');

    foreach ($permissions as $permission) {
    $permission_ids[] = $permission;
    }
    $userName = auth()->guard('admin')->user()->firstname." ".auth()->guard('admin')->user()->lastname;
    $user_permission = Spatie\Permission\Models\Permission::where('name', 'users')->first();
    $order_permission = Spatie\Permission\Models\Permission::where('name', 'order')->first();
    $counter_permission = Spatie\Permission\Models\Permission::where('name', 'new_order')->first();
    $report_permission = Spatie\Permission\Models\Permission::where('name', 'reports')->first();

@endphp

{{-- Page Title --}}
<div class="pagetitle">
    <h1>Dashboard</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
</div>


{{-- Dashboard Section --}}
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
        <div class="col-lg-12 m-3">
            <h2 align="center" class="">Welcome to Dashboard {{$userName}} </h2>
        </div>
        <div class="col-md-12">
            <div class="row">
            @if(in_array($counter_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5></h5>
                            <a href="{{route('orders.create')}}">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                                <div class="ps-3">
                                    <h4><b>New Order</b></h4>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5></h5>
                            <a href="{{route('qrpage')}}">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-qrcode"></i>
                                </div>
                                <div class="ps-3">
                                    <h4><b>Scan QrCode</b></h4>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                 @if(in_array($report_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5></h5>
                            <a href="{{route('reports.order_history')}}">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file"></i>
                                </div>
                                <div class="ps-3">
                                    <h5><b>Order History Report</b></h5>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                @if(in_array($report_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5></h5>
                            <a href="{{route('reports.department_pending_orders')}}">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file"></i>
                                </div>
                                <div class="ps-3">
                                    <h5><b>Department Pending Report</b></h5>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                @if(in_array($report_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5></h5>
                            <a href="{{route('reports.performance')}}">
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file"></i>
                                </div>
                                <div class="ps-3">
                                    <h5><b>Department Performance Report</b></h5>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                @if(in_array($user_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5>Admin Users</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-users"></i>
                                </div>
                                <div class="ps-3">
                                    <h4><b>{{$userCount}}</b></h4>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                                    <a href="{{route('users')}}">View all Users<i
                                            class="fa-solid fa-right-long ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if(in_array($order_permission->id,$permission_ids))
                <div class="col-md-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <h5>Orders</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-bars"></i>
                                </div>
                                <div class="ps-3">
                                    <h4><b>{{$ordersCount}}</b></h4>
                                </div>
                            </div>
                            <div class="dash_card_title mt-1">
                                    <a href="{{route('order')}}">View all Orders<i
                                            class="fa-solid fa-right-long ms-2"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>

@endsection

{{-- Custom JS --}}
@section('page-js')

<script type="text/javascript">
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    timeOut: 10000
}

@if(Session::has("success"))
    toastr.success("{{ Session::get("success")}}");
@endif

// @if (Session::has('error'))
//     toastr.error('{{ Session::get('error') }}')
// @endif
</script>

@endsection
