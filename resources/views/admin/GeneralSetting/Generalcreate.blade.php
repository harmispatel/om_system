@extends('admin.layouts.admin-layout')
@section('title', 'SETTINGS - Order Management System')
@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Settings</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">settings</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Page Section --}}
    <section class="section dashboard">
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <form method="post" action="{{ route('General.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="padding-left: 1.5em;">
                                        <label class="form-label"><strong>Working Days</strong></label>
                                    </div>
                                    <div class="form-group mb-3">
                                        <div class="form-check form-check-inline">
                                            @foreach ($generalSettings as $generalSetting)
                                                <div class="form-check form-check-inline">
                                                    <input type="text" name="Days[]" class="form-check-input" value="{{ $generalSetting->Days }}">
                                                    <input type="checkbox" name="holiday[{{ $generalSetting->Days }}]" class="form-check-input" {{ $generalSetting->holiday == 'on' ? 'checked' : '' }}>
                                                    <label for="{{ $generalSetting->Days }}" class="form-check-label">{{ ucfirst($generalSetting->Days) }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="form-group" style="padding-left: 1.5em;">
                                        <label class="form-label"><strong>Working Hours</strong></label>
                                    </div>
                                    <div class="form-group mb-3" style="padding-left: 1.5em;">
                                        <div class="row">
                                            <div class="col-md-2 mb-2">
                                                <input type="time" name="StartTime" class="{{ $errors->has('StartTime') ? 'is-invalid' : '' }} form-control" value="{{$generalSetting->StartTime}}" id="from_time">
                                                @if ($errors->has('StartTime'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('StartTime') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-1 text-center">
                                                <label class="form-label"><strong>To</strong></label>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="time" name="EndTime" class="{{ $errors->has('EndTime') ? 'is-invalid' : '' }} form-control" value="{{$generalSetting->EndTime}}" id="to_time">
                                                @if ($errors->has('EndTime'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('EndTime') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" style="padding-left: 1.5em;">
                                        <button class="btn form_button">Update</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page-js')

    <script type="text/javascript">
        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif
    </script>

@endsection
