@extends('admin.layouts.admin-layout')

@section('title', 'Task-Management List')

@section('content')

{{-- Page Title --}}
<div class="pagetitle">
    <h1>Task-Schedule List</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Task-Management List</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- List of task-schedules Section --}}
<section class="section dashboard">
<div class="row text-end">
        <div class="col-md-12 p-3">
            <a href="{{route('task-management')}}" class="btn custom-btn btn-sm"><i class="fa fa-plus"></i></a>
        </div>
    </div>
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

        {{-- schedule Listing Card --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                    </div>
                    <div class="table-responsive custom_dt_table">
                        <table class="table w-100" id="TaskScheduleTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>First Task-Name</th>
                                    <th>Second Task-Name</th>
                                    <th>Time Duretion Between Task</th>
                                    <th>Types Of Work</th>
                                    <th>actions</th>
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


{{-- Custom JS --}}
@section('page-js')

<script type="text/javascript">

$(function() {

var table = $('#TaskScheduleTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 100,
    ajax: "{{ route('task-manage.list') }}",
    columns: [{
            data: 'id',
            name: 'id'
        },
        {
            data: 'task1_id',
            name: 'task1_id'
        },
        {
            data: 'task2_id',
            name: 'task2_id'
        },
        {
            data: 'works_time',
            name: 'works_time'
        },
        {
            data: 'types_of_works',
            name: 'types_of_works'
        },
        {
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false
        },
    ]
});

});

function deleteOrderRecord(id) {

swal({
        title: "Are you sure You want to Delete It ?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
    .then((willDeleteRole) => {
        if (willDeleteRole) {
            $.ajax({
                type: "POST",
                url: "{{ route('task-manage.destroy') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id,
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.success == 1) {
                        toastr.success(response.message);
                        $('#TaskScheduleTable').DataTable().ajax.reload();
                    } else {
                        swal(response.message, "", "error");
                    }
                }
            });
        } else {
            swal("Cancelled", "", "error");

        }
    });
}

toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    timeOut: 10000
}

@if(Session::has('success')) 
    toastr.success("{{ Session::get("success") }}")
@endif

// @if (Session::has('error'))
//     toastr.error('{{ Session::get('error') }}')
// @endif
</script>

@endsection