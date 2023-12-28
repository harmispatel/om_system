@extends('admin.layouts.admin-layout')
@section('title')
Reasons Of Delay-Time
@endsection
@section('content')
<!-- @if(Session::has('success'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>{{ Session::get('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif -->
<div class="pagetitle">
    <h1>Reasons Of DelayTime</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="{{route('reasons')}}">List Of Reasons</a></li>
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
                <form class="form" id="form" action="{{route('reasons.store')}}" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        @csrf
                        <div class="form_box">
                            <div class="form_box_inr">
                                <div class="box_title">
                                    <h2>Add Reasons Of Delay-Time</h2>
                                </div>
                                <div class="form_box_info">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label for="department" class="form-label">Departments <span class="text-danger">*</span></label>
                                            <select name="department" id="department" class="form-select {{ $errors->has('department') ? 'is-invalid' : '' }}">
                                            <option value="" selected>Select Department</option>
                                                @if(count($departments) > 0)

                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department->id }}" {{ (old('department') == $department->id) ? 'selected' : '' }}>
                                                            {{ $department->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('department'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('department') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8 mb-3">
                                            <label for="reason" class="form-label">Reason Of Delay<span class="text-danger">*</span></label>
                                            <input type="text" name="reason" id="reason" value="{{ (old('reason')) ? old('reason') : '' }}" class="form-control {{ $errors->has('reason') ? 'is-invalid' : '' }}">
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
