@php
    $user_dt = Auth::guard('admin')->user();
    $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
@endphp

@extends('admin.layouts.admin-layout')
@section('title', 'Dashboard - Order Management System')
@section('content')

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
        <div class="col-md-12">
            <div class="row">
                @if($user_type == 1 || $user_type == 2)
                    <div class="col-md-4 mb-3">
                        <div class="info_card">
                            <div class="info_card_inr">
                                <a href="{{route('orders.create')}}">
                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="fa-solid fa-plus"></i></div>
                                        <div class="ps-3">
                                            <h5><b>Create / Repeate Order</b></h5>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-4 mb-3">
                    <div class="info_card">
                        <div class="info_card_inr">
                            <a href="{{route('qrpage')}}">
                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"><i class="fa-solid fa-qrcode"></i></div>
                                    <div class="ps-3">
                                        <h5><b>Qr Scanner</b></h5>
                                    </div>
                                </div>
                            </a>
                        </div>
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

    @if(Session::has("success"))
        toastr.success("{{ Session::get("success")}}");
    @endif

</script>
@endsection
