@extends('admin.layouts.admin-layout')
@section('title', 'Order Management System | Task Management - Create')
@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Task Management</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('task-manage.list') }}">Task-List</a></li>

                        <li class="breadcrumb-item active">Create Task Management</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Task Management Section --}}
    <section class="section dashboard">
        <div class="col-md-12">
            <div class="card">
                <form class="form" id="form" action="{{ route('task-manage.create') }}" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        @csrf
                        <div class="form_box">
                            <div class="form_box_inr">
                                <div class="box_title">
                                    <h2>Task Management Details</h2>
                                </div>
                                <div class="form_box_info">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="type_of_work" class="form-label">Types Of Works <span class="text-danger">*</span></label>
                                            <select name="type_of_work" id="type_of_work" class="form-select {{ $errors->has('type_of_work') ? 'is-invalid' : '' }}">
                                               
                                                @if(count($types_of_works) > 0)
                                                   
                                                    @foreach ($types_of_works as $type_of_work)
                                                        <option value="{{ $type_of_work->id }}" {{ (old('type_of_work') == $type_of_work->id) ? 'selected' : '' }}>
                                                            {{ $type_of_work->types_of_works }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if ($errors->has('type_of_work'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('type_of_work') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="work_hours" class="form-label">Work Hours <span class="text-danger">*</span></label>
                                            <input type="number" name="work_hours" id="work_hours" value="{{ (old('work_hours')) ? old('work_hours') : '00' }}" class="form-control {{ $errors->has('work_hours') ? 'is-invalid' : '' }}">
                                            @if ($errors->has('work_hours'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('work_hours') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="work_minutes" class="form-label">Work Minutes <span class="text-danger">*</span></label>
                                            <input type="number" name="work_minutes" id="work_minutes" value="{{ (old('work_minutes')) ? old('work_minutes') : '00' }}" class="form-control {{ $errors->has('work_minutes') ? 'is-invalid' : '' }}">
                                            @if ($errors->has('work_minutes'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('work_minutes') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-2 mb-3">
                                            <label for="work_seconds" class="form-label">Work Seconds <span class="text-danger">*</span></label>
                                            <input type="number" name="work_seconds" id="work_seconds" value="{{ (old('work_seconds')) ? old('work_seconds') : '00' }}" class="form-control {{ $errors->has('work_seconds') ? 'is-invalid' : '' }}">
                                            @if ($errors->has('work_seconds'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('work_seconds') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">In-Switch <span class="text-danger">*</span></label><br>
                                            <div style="border: 1px solid {{ ($errors->has('switch1')) ? 'red' : '#ced4da' }}; padding: 10px; border-radius: 0.375rem">
                                                @if (count($inSwitches) > 0)
                                                    @foreach ($inSwitches as $switch)
                                                        <div class="mb-1">
                                                            <input type="radio" name="switch1" id="switch1_{{ $switch['id'] }}" {{ (old('switch1') == $switch['id']) ? 'checked' : '' }} value="{{ $switch['id'] }}"> <label for="switch1_{{ $switch['id'] }}" style="cursor: pointer; font-weight:700; font-size:15px;">{{ $switch['name'] }}</label>
                                                        </div> <hr style="margin: 0.5rem 0">
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if ($errors->has('switch1'))
                                                <div class="text-danger" style="margin-top: 0.25rem;">
                                                    {{ $errors->first('switch1') }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Out-Switch<span class="text-danger">*</span></label><br>
                                            <div style="border: 1px solid {{ ($errors->has('switch2')) ? 'red' : '#ced4da' }}; padding: 10px; border-radius: 0.375rem">
                                                @if (count($outSwitches) > 0)
                                                    @foreach ($outSwitches as $switch)
                                                        <div class="mb-1">
                                                            <input type="radio" name="switch2" id="switch2_{{ $switch['id'] }}" {{ (old('switch2') == $switch['id']) ? 'checked' : '' }} value="{{ $switch['id'] }}"> <label for="switch2_{{ $switch['id'] }}" style="cursor: pointer; font-weight:700; font-size:15px;">{{ $switch['name'] }}</label>
                                                        </div> <hr style="margin: 0.5rem 0">
                                                    @endforeach
                                                @endif
                                            </div>
                                            @if ($errors->has('switch2'))
                                                <div class="text-danger" style="margin-top: 0.25rem;">
                                                    {{ $errors->first('switch2') }}
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
    </section>

@endsection

{{-- Custom JS --}}
@section('page-js')
    <script type="text/javascript">

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>

@endsection
