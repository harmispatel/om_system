@extends('admin.layouts.admin-layout')
@section('title', 'Order Management System | Reports | Types of Works Pending')
@section('content')

    {{--page title--}}
    <div class="pagetitle">
        <h1>Reports</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Types of Works Pending Report</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Types of Works Pending Report Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div class="row">
                                <div class="col-md-6">
                                    <select id="type_of_work" name="type_of_work" class="form-select">
                                        <option value="0">All Types of Works</option>
                                        @foreach($types_of_works as $type_of_work)
                                            <option value="{{$type_of_work->id}}">{{$type_of_work->types_of_works}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="TypesofWorksPendingReport">
                                <thead>
                                    <tr>
                                        <th>Order No</th>
                                        <th>Customer Name</th>
                                        <th>Customer Contact-No</th>
                                        <th>User Name</th>
                                        <th>Switch IN & Time</th>
                                        <th style="width: 20%;">Remainig Time</th>
                                        <th>Actions</th>
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

            var table = $('#TypesofWorksPendingReport').DataTable({
                lengthChange: false,
                // searching: false,
                processing: true,
                serverSide: true,
                pageLength: 100,
                ajax: {
                    url:"{{ route('reports.typesofworks_pending') }}",
                    data:function(d){
                        d.type_of_work = $('#type_of_work').val();
                    }
                },
                columns: [
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
                    {
                        data: 'actions',
                        name: 'actions'
                    },
                ]
            });

            $('#type_of_work').on('change', function() {
                table.ajax.reload(); // Redraw the DataTable with the new filter
            });
        });

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
@endsection
