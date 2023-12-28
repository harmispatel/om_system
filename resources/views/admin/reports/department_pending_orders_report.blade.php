@extends('admin.layouts.admin-layout')
@section('title', 'Order Management System | Reports | Department Pending Orders')
@section('content')

    {{--page title--}}
    <div class="pagetitle">
        <h1>Reports</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Department Pending Orders Report</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Department Pending Report Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                    <div class="col-md-6">
                                        {{-- <label for="role">Select Department:</label> --}}
                                        <select id="department" name="department" class="form-select">
                                            <option value="0">All Departments</option>
                                            @foreach($departments as $department)
                                            <option value="{{$department->id}}">{{$department->name}}</option>
                                            @endforeach
                                            <!-- Add more options as needed -->
                                        </select>
                                    </div>
                                    <!-- <div class="col-md-1">-->
                                    <!-- </div>-->
                                    <!-- <div class="col-md-2">-->
                                    <!--     <label for="form-control">Start Date</label>-->
                                    <!--     <input type="date" name="startdate" id = "startdate" class="form-control" placeholder="start Date">-->
                                    <!--</div>-->
                                    <!-- <div class="col-md-2">-->
                                    <!--     <label for="form-control">End Date</label>-->
                                    <!--     <input type="date" name="enddate" id = "enddate" class="form-control" placeholder="End Date">-->
                                    <!-- </div>-->
                                    <!-- <div class="col-md-1">-->
                                    <!--    <button id="search" class="btn custom-btn mt-4"><i class="fa fa-search"></i></i></button>-->
                                    <!-- </div>-->
                            </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="OrderHistoryTable">
                                <thead>
                                    <tr>
                                        <!--<th>Serial No</th>-->
                                        <th>Order No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Contact-No</th>
                                        <th>User Name</th>
                                        <th>Switch IN & Time</th>
                                        <th style="width: 20%;">Remainig Time</th>
                                        <!--<th>Created Date</th>-->
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

            var table = $('#OrderHistoryTable').DataTable({
                lengthChange: false,
                processing: true,
                serverSide: true,
                pageLength: 100,
                ajax: {
                    url:"{{ route('reports.department_pending_orders') }}",
                    data:function(d){
                        d.department = $('#department').val();
                        d.startDate = $('#startdate').val();
                        d.endDate= $('#enddate').val();
                    }
                },
                columns: [
                    // {
                    //     data: 'id',
                    //     name: 'id'
                    // },
                    {
                        data: 'order_no',
                        name: 'order_no'
                    },
                    {
                        data:'customer_name',
                        name:'customer_name'
                    },
                    {
                        data:'mobile_no',
                        name:'mobile_no'
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'inswitch_time',
                        name: 'inswitch_time'
                    },
                    {
                        data: 'remainig_time',
                        name: 'remainig_time'
                    },
                    // {
                    //     data: 'created_date',
                    //     name: 'created_date'
                    // },
                ]
                });

                $('#department').on('change', function() {
                        table.ajax.reload(); // Redraw the DataTable with the new filter
                });

                //   $('#search').on('click',function(){
                //       table.ajax.reload();
                // });
        });

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
@endsection
