@extends('admin.layouts.admin-layout')
@section('content')

{{--page title--}}
<div class="pagetitle">
    <h1>Report</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Department-Performance Report</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- section for Report --}}
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
    </div>

    <div class="row">

        {{-- Order Listing Card --}}
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <div class="card-title p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="department">Select Department:</label>
                                <select id="department" name="department" class="form-select">
                                    <option value="0">All Departments</option>
                                    @foreach($departments as $department)
                                    <option value="{{$department->id}}">{{$department->name}}</option>
                                    @endforeach
                                    <!-- Add more options as needed -->
                                </select>
                            </div>
                            <div class="col-md-1">
                             </div>
                             <div class="col-md-2">
                                 <label for="form-control">Start Date</label>
                                 <input type="date" name="startdate" id = "startdate" class="form-control" placeholder="start Date">
                            </div>
                             <div class="col-md-2">
                                 <label for="form-control">End Date</label>
                                 <input type="date" name="enddate" id = "enddate" class="form-control" placeholder="End Date">
                             </div>
                             <div class="col-md-1">
                                <button id="search" class="btn custom-btn mt-4"><i class="fa fa-search"></i></i></button>
                             </div>
                            <div class="col-md-6 mt-3" >
                                <label>Performance : </label>
                                <input type="text" class="rounded-circle" style="width:50px;" value="50%"disabled>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive custom_dt_table mt-2">
                        <table class="table w-100" id="PerformanceTable">
                            <thead>
                                <tr>
                                    <th>Serial No</th>
                                    <th>Order No</th>
                                    <th>Customer-Name</th>
                                    <th>Contact-No</th>
                                    <th>SwitchIn Date/Name</th>
                                    <th>SwitchOut Time/Name</th>
                                    <th>Duration</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('page-js')

<script type="text/javascript">
    $(function() {

        var table = $('#PerformanceTable').DataTable({
            paging:false,
            processing: true,
            serverSide: true,
            pageLength: 100,
            ajax: {
                url: "{{ route('reports.performance') }}",
                data:function(d){
                    d.department = $('#department').val();
                    d.startDate = $('#startdate').val();
                    d.endDate= $('#enddate').val();
                }
            },
            columns: [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'order_no',
                    name: 'order_no'
                },
                {
                    data:'customer_name',
                    name:'customer_name'
                },
                {
                    data: 'mobile_no',
                    name: 'mobile_no'
                },
                {
                    data: 'inswitch_time',
                    name: 'inswitch_time'
                },
                {
                    data: 'outswitch_time',
                    name: 'outswitch_time'
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'created_date',
                    name: 'created_date'
                },
            ]
        });


        $('#department').on('change', function() {
            table.ajax.reload(); // Redraw the DataTable with the new filter
        });

        $('#search').on('click',function(){
            table.ajax.reload();
        });


    });



    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        timeOut: 10000
    }

    @if(Session::has('success'))
    toastr.success('{{ Session::get('success') }}')
    @endif

</script>

@endsection
