@extends('admin.layouts.admin-layout')
@section('title', 'Task-Management')
@section('content')
    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Factory Time Schedules</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Time Schedules</li>
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
                    <h5 class="card-title" align="center"> Time Table </h5>
                    <form method="post" action="{{ route('General.store') }}">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <h4>Days</h4>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check form-check-inline">
                                                @foreach ($generalSettings as $generalSetting)
                                                    <div class="form-check form-check-inline">
                                                        <input type="text" name="Days[]" class="form-check-input" value="{{ $generalSetting->Days }}">
                                                        <input type="checkbox" name="holiday[{{ $generalSetting->Days }}]" 
                                                            class="form-check-input" {{ $generalSetting->holiday == 'on' ? 'checked' : '' }}>
                                                        <label for="{{ $generalSetting->Days }}"
                                                            class="form-check-label">{{ ucfirst($generalSetting->Days) }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <h4>Time</h4>
                                            <input type="time" name="StartTime"
                                                class="{{ $errors->has('StartTime') ? 'is-invalid' : '' }}"
                                                value="{{$generalSetting->StartTime}}"> To
                                            <input type="time" name="EndTime"
                                                class="{{ $errors->has('EndTime') ? 'is-invalid' : '' }}"
                                                value="{{$generalSetting->EndTime}}">
                                            @if ($errors->has('StartTime'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('StartTime') }}
                                                </div>
                                            @elseif ($errors->has('EndTime'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('EndTime') }}
                                                </div>
                                            @else
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="card-footer text-center mt-5">
                                <button class="btn form_button">{{ __('Save') }}</button>
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
