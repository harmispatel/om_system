@extends('admin.layouts.admin-layout')
@section('title', 'Task-Management')
@section('content')
{{-- Page Title --}}
<div class="pagetitle">
    <h1>Task-Management</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                     <li class="breadcrumb-item"><a href="{{ route('task-manage.list') }}">Task-Management List</a></li>
                    <li class="breadcrumb-item active">edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- Page Section --}}
<section class="section dashboard">
    <!--<div class="row text-end">-->
    <!--    <div class="col-md-12">-->
    <!--        <a href="{{route('task-manage.list')}}" class="btn btn-warning">Listing Of Assigned Tasks</a>-->
    <!--    </div>-->
    <!--</div>-->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <form method="post" action="{{route('task-manage.update')}}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" name="id" id="id" value="{{encrypt($taskManage->id)}}">
                           <div class="col-md-12">
                            {{-- <div class="col-md-6 mb-3">
                                <div class="form-group">
                                   
                                    <label for="types_of_works" class="form-label">Types Of Work<span
                                            class="text-danger">*</span></label>
                                    <select type="text" name="types_of_works" id="select-box"
                                        class="form-control {{ $errors->has('types_of_works') ? 'is-invalid' : '' }}">
                                        <option value="">--select value--</option>
                                        @foreach ($TypesOf_Works as $TypesOf_Work)
                                            <option value="{{ $TypesOf_Work->id }} "
                                                {{ old('types_of_works') == $TypesOf_Work->id ? 'selected' : '' }}>
                                                {{ $TypesOf_Work->types_of_works }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('types_of_works'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('types_of_works') }}
                                        </div>
                                    @endif
                                </div>
                            </div> --}}
                           </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                {{-- First Task --}}
                                <div class="accordion" id="accordionFive">
                                    {{-- <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapsefirst"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                First-List Of Task
                                            </button>
                                        </h2>
                                        <div id="collapsefirst" class="accordion-collapse collapse"
                                            aria-labelledby="headingFive" data-bs-parent="#accordionFive">
                                            @foreach ($permission->slice(10, 21) as $value)
                                            <div class="accordion-body">
                                                <label>
                                                    <input type="checkbox" name="task1_id" value="{{ $value->id }}"
                                                        class="mr-3 {{ $errors->has('task1_id') ? 'is-invalid' : '' }}" onclick="handleCheckbox1(this)">
                                                        
                                                    @if($value->name == 'new_order')
                                                    New Order
                                                    @elseif($value->name == 'repeat_order')
                                                    Repeat Order
                                                    @elseif($value->name == 'iss.for.des/cam')
                                                    Issue To Design-Department
                                                    @elseif($value->name == 'rec.for.des/cam')
                                                    Receive For Design-Department
                                                    @elseif($value->name == 'qc&iss.for.waxing')
                                                    Quality & Issue To Waxing-Department
                                                    @elseif($value->name == 'req.for.waxing')
                                                    Receive For Waxing-Department
                                                    @elseif($value->name == 'qc&iss.for.casting')
                                                    Quality & Issue To Casting-Department
                                                    @elseif($value->name == 'req.for.casting')
                                                    Receive For Casting-Department
                                                    @elseif($value->name == 'iss.for.hisab')
                                                    Issue To Hisab-Department
                                                    @elseif($value->name == 'req.for.hisab')
                                                    Receive For Hisab-Department
                                                    @elseif($value->name == 'qc&iss.for.del/cen')
                                                    Quality & Issue To Delivery/Central
                                                    @elseif($value->name == 'req.for.del/cen')
                                                    Receive For Delivery/Central
                                                    @elseif($value->name == 'iss.for.ready')
                                                    Issue To Ready-Department
                                                    @elseif($value->name == 'req.for.ready')
                                                    Receive For Ready-Department
                                                    @elseif($value->name == 'delivery/complete')
                                                    Delivery/Complete
                                                    @elseif($value->name == 'iss.for.delivery')
                                                    Issue To Delivery-Department
                                                    @elseif($value->name == 'rec.for.delivery')
                                                    Receive For Delivery-Department
                                                    @elseif($value->name == 'iss.for.packing')
                                                    Issue To Packing-Department
                                                    @elseif($value->name == 'rec.for.packing')
                                                    Receive For Packing-Department
                                                    @elseif($value->name == 'iss.for.saleing')
                                                    Issue To Saleing-Department
                                                    @else
                                                    Time Management
                                                    @endif
                                                </label>
                                            </div>
                                            @endforeach
                                            @if ($errors->has('task1_id'))
                                                            <div class="invalid-feedback">
                                                                {{ $errors->first('task1_id') }}
                                                            </div>
                                                        @endif
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="col-md-4">
                                {{-- Second Task --}}
                                {{-- <div class="accordion" id="accordionFive">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingFive">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapsesecond"
                                                aria-expanded="false" aria-controls="collapseFive">
                                                Second-List Of Task
                                            </button>
                                        </h2>
                                        <div id="collapsesecond" class="accordion-collapse collapse"
                                            aria-labelledby="headingFive" data-bs-parent="#accordionFive">
                                            @foreach ($permission->slice(10, 21) as $value)
                                                    <div class="accordion-body">
                                                        <label>
                                                            <input type="checkbox" name="task2_id" value="{{ $value->id }}"
                                                                class="mr-3 {{ $errors->has('task2_id') ? 'is-invalid' : '' }}" onclick="handleCheckbox2(this)">
                                                                
                                                                @if($value->name == 'new_order')
                                                            New Order
                                                            @elseif($value->name == 'repeat_order')
                                                            Repeat Order
                                                            @elseif($value->name == 'iss.for.des/cam')
                                                            Issue To Design-Department
                                                            @elseif($value->name == 'rec.for.des/cam')
                                                            Receive For Design-Department
                                                            @elseif($value->name == 'qc&iss.for.waxing')
                                                            Quality & Issue To Waxing-Department
                                                            @elseif($value->name == 'req.for.waxing')
                                                            Receive For Waxing-Department
                                                            @elseif($value->name == 'qc&iss.for.casting')
                                                            Quality & Issue To Casting-Department
                                                            @elseif($value->name == 'req.for.casting')
                                                            Receive For Casting-Department
                                                            @elseif($value->name == 'iss.for.hisab')
                                                            Issue To Hisab-Department
                                                            @elseif($value->name == 'req.for.hisab')
                                                            Receive For Hisab-Department
                                                            @elseif($value->name == 'qc&iss.for.del/cen')
                                                            Quality & Issue To Delivery/Central
                                                            @elseif($value->name == 'req.for.del/cen')
                                                            Receive For Delivery/Central
                                                            @elseif($value->name == 'iss.for.ready')
                                                            Issue To Ready-Department
                                                            @elseif($value->name == 'req.for.ready')
                                                            Receive For Ready-Department
                                                            @elseif($value->name == 'delivery/complete')
                                                            Delivery/Complete
                                                            @elseif($value->name == 'iss.for.delivery')
                                                            Issue To Delivery-Department
                                                            @elseif($value->name == 'rec.for.delivery')
                                                            Receive For Delivery-Department
                                                            @elseif($value->name == 'iss.for.packing')
                                                            Issue To Packing-Department
                                                            @elseif($value->name == 'rec.for.packing')
                                                            Receive For Packing-Department
                                                            @elseif($value->name == 'iss.for.saleing')
                                                            Issue To Saleing-Department
                                                            @else
                                                            Time Management
                                                            @endif
                                                        </label>
                                                    </div>
                                            @endforeach
                                        </div>
                                        @if ($errors->has('task2_id'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('task2_id') }}
                                        </div>
                                    @endif
                                    </div>
                                </div> --}}
                            </div>   

                            <div class="row mt-2">
                                <h3>Working Time<span class="text-danger">*</span></h3>
                                <label for="working_hours" class="form-label">Hours*
                                    &nbsp;&nbsp;&nbsp;&nbsp;Minutes*&nbsp;&nbsp;&nbsp;Seconds*</label>
                                <div class="col-md-1 form-group">

                                    <input
                                        class="form-control-sm border border-dark {{ $errors->has('working_hours') ? 'is-invalid' : '' }}"
                                        type="number" name="working_hours" min="00" max="200" value="{{$taskManage->working_hours}}">
                                
                                </div> 
                                <div class="col-md-1 form-group">

                                    <input
                                        class="form-control-sm border border-dark {{ $errors->has('working_minutes') ? 'is-invalid' : '' }}"
                                        type="number" name="working_minutes" min="00" max="59" value="{{$taskManage->working_minutes}}">
                                  
                                </div>   
                                <div class="col-md-1 form-group"> 
                                    <input
                                        class="form-control-sm border border-dark "
                                        type="number" name="working_seconds" value="00" min="00" max="59" value="{{$taskManage->working_seconds}}">
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

function handleCheckbox1(checkbox) {
    var checkboxes = document.getElementsByName("task1_id");

    checkboxes.forEach(function(currentCheckbox) {
        if (currentCheckbox !== checkbox) {
            currentCheckbox.checked = false;
        }
    });
}
function handleCheckbox2(checkbox) {
    var checkboxes2 = document.getElementsByName("task2_id");

    checkboxes2.forEach(function(currentCheckbox) {
        if (currentCheckbox !== checkbox) {
            currentCheckbox.checked = false;
        }
    });
}

        @if(Session::has('success'))
        toastr.success('{{ Session::get('success') }}')
        @endif
        
        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif
</script>

@endsection        