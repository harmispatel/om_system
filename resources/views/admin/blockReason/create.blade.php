@extends('admin.layouts.admin-layout')
@section('title')
Reasons Of Delay-Time
@endsection
@section('content')

<div class="pagetitle">
    <h1>Create Block Reasons</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('block-reasons')}}">List Of Reasons</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>

</div>

<section class="section dashboard">
        <div class="row">

            {{-- Reason Card --}}
            <div class="col-md-12">
                <div class="card">
                <form class="form" id="form" action="{{route('block-reasons.store')}}" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        @csrf
                        <div class="form_box">
                            <div class="form_box_inr">
                                <div class="box_title">
                                    <h2>Add Reasons For Block-Order</h2>
                                </div>
                                <div class="form_box_info">
                                    <div class="row">
                                        <div class="col-md-8 mb-3">
                                            <label for="reason" class="form-label">Enter Your Reason<span class="text-danger">*</span></label>
                                            <textarea type="text" name="reason" id="reason" value="{{ (old('reason')) ? old('reason') : '' }}" class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}"></textarea>
                                            @if ($errors->has('reason'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('reason') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <button class="btn form_button">Save</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('page-js')
<script type="text/javascript">

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
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
