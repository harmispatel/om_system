@extends('admin.layouts.admin-layout')

@section('title', 'Departments')

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Roles</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('roles') }}">Roles</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>

        </div>
    </div>
    {{-- New Clients add Section --}}
    <section class="section dashboard">
        <div class="row">

            {{-- Clients Card --}}
            <div class="col-md-12">
                <div class="card">

                    <form class="form" action="{{ route('roles.update') }}" method="POST" enctype="multipart/form-data">

                        <div class="card-body">
                            @csrf
                            <div class="form_box">
                                <div class="form_box_inr">
                                    <div class="box_title">
                                        <h2>Roles Details</h2>
                                    </div>
                                    <div class="form_box_info">
                                        <input type="hidden" name="id" value="{{ encrypt($role->id) }}">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="firstname" class="form-label">Name <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" id="name"
                                                        class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                        placeholder="Enter Name"
                                                        value="{{ isset($role->name) ? $role->name : '' }}">
                                                    @if ($errors->has('name'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('name') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="is_counter" class="form-label">Is_Counter? <span
                                                        class="text-danger">*</span></label>
                                                <input type="checkbox" id="is_counter" name="is_counter" value="1" {{ ($role->is_counter = 1) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-6 mb-3" id="select-box">
                                            <div class="form-group" >
                                                <label for="order" class="form-label">Order Value</label>
                                                <select type="text" name="order_value"  class="form-control">
                                                    <option value="">--select value--</option>
                                                    <option value="C">C</option>
                                                    <option value="B">B</option>
                                                    <option value="R">R</option>
                                                    <option value="WH">WH</option>
                                                    <option value="BC">BC</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="prmission_box">
                                            <h3>Permission<span class="text-danger">*</span></h3>
                                            <div class="row">

                                                <div class="col-md-3">
                                                    <div class="accordion" id="accordionOne">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingOne">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#collapseOne"
                                                                    aria-expanded="false" aria-controls="collapseOne">
                                                                    Roles
                                                                </button>
                                                            </h2>
                                                            <div id="collapseOne" class="accordion-collapse collapse"
                                                                aria-labelledby="headingOne" data-bs-parent="#accordionOne">
                                                                @foreach ($permission->slice(0, 4) as $value)
                                                                    <div class="accordion-body">
                                                                        <label>
                                                                            <input type="checkbox" name="permission[]"
                                                                                value="{{ $value->id }}" class="mr-3"
                                                                                {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                                            @if ($value->name == 'roles')
                                                                                View
                                                                            @elseif($value->name == 'roles.create')
                                                                                Add
                                                                            @elseif($value->name == 'roles.edit')
                                                                                Update
                                                                            @else
                                                                                Delete
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="accordion" id="accordionFive">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFive">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#collapseFive"
                                                                    aria-expanded="false" aria-controls="collapseFive">
                                                                    Users
                                                                </button>
                                                            </h2>
                                                            <div id="collapseFive" class="accordion-collapse collapse"
                                                                aria-labelledby="headingFive"
                                                                data-bs-parent="#accordionFive">
                                                                @foreach ($permission->slice(4, 4) as $value)
                                                                    <div class="accordion-body">
                                                                        <label>
                                                                            <input type="checkbox" name="permission[]"
                                                                                value="{{ $value->id }}"
                                                                                class="mr-3"
                                                                                {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                                            @if ($value->name == 'users')
                                                                                View
                                                                            @elseif($value->name == 'users.create')
                                                                                Add
                                                                            @elseif($value->name == 'users.edit')
                                                                                Update
                                                                            @else
                                                                                Delete
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="accordion" id="accordionThirteen">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="heading">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseThirteen"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseThirteen">
                                                                    Orders
                                                                </button>
                                                            </h2>
                                                            <div id="collapseThirteen" class="accordion-collapse collapse"
                                                                aria-labelledby="heading"
                                                                data-bs-parent="#accordionThirteen">
                                                                @foreach ($permission->slice(8, 1) as $value)
                                                                    <div class="accordion-body">
                                                                        <label>
                                                                            <input type="checkbox" name="permission[]"
                                                                                value="{{ $value->id }}"
                                                                                class="mr-3" {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                                                @if($value->name == 'order')
                                                                                 View
                                                                                 @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="accordion" id="accordionThirteen">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="heading">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#collapseReport" aria-expanded="false"
                                                                    aria-controls="collapseThirteen">
                                                                    Reports
                                                                </button>
                                                            </h2>
                                                            <div id="collapseReport" class="accordion-collapse collapse"
                                                                aria-labelledby="heading"
                                                                data-bs-parent="#accordionThirteen">
                                                            

                                                                @foreach ($permission->slice(9, 1) as $value)
                                                                    <div class="accordion-body">
                                                                        <label>
                                                                            <input type="checkbox" name="permission[]"
                                                                                value="{{ $value->id }}"
                                                                                class="mr-3 "{{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                                            @if ($value->name == 'reports')
                                                                                View
                                                                            @endif
                                                                        </label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-3">
                                                    <div class="accordion" id="accordionFive">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="headingFive">
                                                                <button class="accordion-button collapsed" type="button"
                                                                    data-bs-toggle="collapse" data-bs-target="#collapseother"
                                                                    aria-expanded="false" aria-controls="collapseFive">
                                                                    Other Permissions
                                                                </button>
                                                            </h2>
                                                            <div id="collapseother" class="accordion-collapse collapse"
                                                                aria-labelledby="headingFive"
                                                                data-bs-parent="#accordionFive">
                                                                @foreach ($permission->slice(10, 15) as $value)
                                                                <div class="accordion-body">
                                                                    <label>
                                                                        <input type="checkbox" name="permission[]"
                                                                            value="{{ $value->id }}" class="mr-3">
                                                                        @if($value->name == 'new_order')
                                                                         New Order
                                                                        @elseif($value->name == 'repeat_order')
                                                                         Repeat Order 
                                                                        @elseif($value->name == 'iss.for.des/cam')
                                                                        ISS.FOR.DES/CAM
                                                                        @elseif($value->name == 'rec.for.des/cam')
                                                                        REC.FOR.DES/CAM
                                                                        @elseif($value->name == 'qc&iss.for.waxing')
                                                                        QC & ISS.FOR.WAXING
                                                                        @elseif($value->name == 'req.for.waxing')
                                                                        REC.FOR.WAXING
                                                                        @elseif($value->name == 'qc&iss.for.casting')
                                                                        QC & ISS.FOR.CASTING
                                                                        @elseif($value->name == 'req.for.casting')
                                                                        REC.FOR.CASTING
                                                                        @elseif($value->name == 'iss.for.hisab')
                                                                        ISS.FOR.HISAB
                                                                        @elseif($value->name == 'req.for.hisab')
                                                                        REC.FOR.HISAB
                                                                        @elseif($value->name == 'qc&iss.for.del/cen')
                                                                        QC & ISS.FOR.DEL/CEN
                                                                        @elseif($value->name == 'req.for.del/cen')
                                                                        REC.FOR.DEL/CEN
                                                                        @elseif($value->name == 'iss.for.ready')
                                                                        ISS.FOR.READY
                                                                        @elseif($value->name == 'req.for.ready')
                                                                        REC.FOR.READY
                                                                        @elseif($value->name == 'delete/complete')
                                                                        DEL/COMPLATE
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                               </div>
                                               
                                               <div class="col-md-3">
                                                <div class="accordion" id="accordionFive">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header" id="headingFive">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse" data-bs-target="#collapseother"
                                                                aria-expanded="false" aria-controls="collapseFive">
                                                                Other Permissions
                                                            </button>
                                                        </h2>
                                                        <div id="collapseother" class="accordion-collapse collapse"
                                                            aria-labelledby="headingFive"
                                                            data-bs-parent="#accordionFive">
                                                            @foreach ($permission->slice(10, 31) as $value)
                                                            <div class="accordion-body">
                                                                <label>
                                                                    <input type="checkbox" name="permission[]"
                                                                        value="{{ $value->id }}" class="mr-3" {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>{{$value->name}}
                                                                     <!-- @if($value->name == 'new_order')
                                                                     New Order
                                                                    @elseif($value->name == 'repeat_order')
                                                                     Repeat Order 
                                                                    @elseif($value->name == 'iss.for.des/cam')
                                                                    ISS.FOR.DES/CAM
                                                                    @elseif($value->name == 'rec.for.des/cam')
                                                                    REC.FOR.DES/CAM
                                                                    @elseif($value->name == 'qc&iss.for.waxing')
                                                                    QC & ISS.FOR.WAXING
                                                                    @elseif($value->name == 'req.for.waxing')
                                                                    REC.FOR.WAXING
                                                                    @elseif($value->name == 'qc&iss.for.casting')
                                                                    QC & ISS.FOR.CASTING
                                                                    @elseif($value->name == 'req.for.casting')
                                                                    REC.FOR.CASTING
                                                                    @elseif($value->name == 'iss.for.hisab')
                                                                    ISS.FOR.HISAB
                                                                    @elseif($value->name == 'req.for.hisab')
                                                                    REC.FOR.HISAB
                                                                    @elseif($value->name == 'qc&iss.for.del/cen')
                                                                    QC & ISS.FOR.DEL/CEN
                                                                    @elseif($value->name == 'req.for.del/cen')
                                                                    REC.FOR.DEL/CEN
                                                                    @elseif($value->name == 'iss.for.ready')
                                                                    ISS.FOR.READY
                                                                    @elseif($value->name == 'req.for.ready')
                                                                    REC.FOR.READY
                                                                    @else
                                                                    DEL/COMPLATE
                                                                    @endif -->
                                                                </label>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </div>
                                           
                                        </div>
                                    <div class="row mt-2">
                                        <h3>Working Time<span class="text-danger">*</span></h3>
                                        <label for="working_hours" class="form-label">Hours* 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Minutes*</label>
                                        <div class="col-md-3 form-group">
                                         
                                            <input
                                                class="form-control-lg border border-dark {{ $errors->has('working_hours') ? 'is-invalid' : '' }}"
                                                type="number" name="working_hours" value="{{ isset($role->working_hours) ? $role->working_hours : '' }}"min="1" max="24">
                                            @if ($errors->has('working_hours'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('working_hours') }}
                                            </div>
                                            @endif

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn form_button">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
@section('page-js')

    <script type="text/javascript">
        $(document).ready(function() {
            // Get the stored checkbox state from localStorage
            var isChecked = localStorage.getItem('checkboxState') === 'true';

            // Set the checkbox state based on the stored value
            $('#is_counter').prop('checked', isChecked);

            // Show or hide the select box based on the checkbox state
            if (isChecked) {
                $('#select-box').show();
            } else {
                $('#select-box').hide();
            }

            // Update the checkbox state in localStorage when it's changed
            $('#is_counter').change(function() {
                var isChecked = this.checked;

                // Store the checkbox state in localStorage
                localStorage.setItem('checkboxState', isChecked);

                // Show or hide the select box based on the checkbox state
                if (isChecked) {
                    $('#select-box').show();
                } else {
                    $('#select-box').hide();
                }
            });
        });
    </script>
@endsection
