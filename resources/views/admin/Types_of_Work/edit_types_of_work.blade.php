@extends('admin.layouts.admin-layout')

@section('title', 'New Users')

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Type of Works</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('types_work') }}">Type of Works</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    {{-- New User add Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{route('update.types_work')}}" method="POST">
                        <div class="card-body">
                            @csrf
                            <div class="form_box">
                                <div class="form_box_inr">
                                    <div class="box_title">
                                        <h2>Types of Works</h2>
                                    </div>
                                    <div class="form_box_info">
                                        <div class="row">
                                            <input type="hidden" name="id" id="id" value="{{encrypt($role->id)}}">
                                            {{-- <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="types_of_works" class="form-label">Types_of_works <span class="text-danger">*</span></label>
                                                    <input type="text" name="types_of_works" value="{{isset($role->types_of_works) ? $role->types_of_works : old('types_of_works')}}" id="types_of_works" class="form-control {{ $errors->has('types_of_works') ? 'is-invalid' : '' }}" placeholder="Enter types_of_works">
                                                    @if ($errors->has('types_of_works'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('types_of_works') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for = "order_value" class="form-label">Order_value <span class="text-danger">*</span></label>
                                                    <input type="text" name="order_value" value="{{isset($role->order_value) ? $role->order_value : old('order_value')}}" id="order_value" class="form-control {{ $errors->has('order_value') ? 'is-invalid' : '' }}" placeholder="Enter order_value">
                                                    @if ($errors->has('order_value'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('order_value') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div> --}}
                                            {{-- <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="works_time" class="form-label">Works_time <span class="text-danger">*</span></label>
                                                    <input type="time" name="works_time" value="{{isset($role->works_time) ? $role->works_time : old('works_time')}}" id="works_time" class="form-control {{$errors->has('works_time') ? 'is-invalid' : ''}}">
                                                    @if ($errors->has('works_time'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('works_time') }}
                                                    </div>
                                                @endif
                                                </div>
                                            </div>        --}}
                                            <div class="row mt-2">
                                                <h3>Working Time<span class="text-danger">*</span></h3>
                                                <label for="working_hours" class="form-label">Hours*
                                                    &nbsp;&nbsp;&nbsp;&nbsp;Minutes*&nbsp;&nbsp;&nbsp;Seconds*</label>
                                                <div class="col-md-1 form-group">
    
                                                    <input
                                                        class="form-control-sm border border-dark {{ $errors->has('working_hours') ? 'is-invalid' : '' }}"
                                                        type="number" name="working_hours" min="0" max="500" value="{{ $role->working_hours }}">
                                                
                                                </div> 
                                                <div class="col-md-1 form-group">
    
                                                    <input
                                                        class="form-control-sm border border-dark {{ $errors->has('working_minutes') ? 'is-invalid' : '' }}"
                                                        type="number" name="working_minutes" min="0" max="59" value="{{ $role->working_minutes }}">
                                                  
                                                </div>   
                                                <div class="col-md-1 form-group"> 
                                                    <input
                                                        class="form-control-sm border border-dark "
                                                        type="number" name="working_seconds" min="0" max="59" value="{{ $role->working_seconds }}">
                                                </div>        
                                                    @if ($errors->has('working_hours') || $errors->has('working_minutes'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('working_hours', 'Working Time Field is Required'),
                                                                $errors->first('working_minutes', 'Working Time Field is Required'),
                                                                 }}                                 
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

{{-- Custom JS --}}
@section('page-js')

    <script type="text/javascript">

        toastr.options =
        {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            timeOut: 10000
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        // @if (Session::has('error'))
        //     toastr.error('{{ Session::get('error') }}')
        // @endif

    </script>

@endsection