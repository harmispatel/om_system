@extends('admin.layouts.admin-layout')
@section('title')
Reasons Of Delay-Time
@endsection
@section('content')
@if(Session::has('success'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <strong>{{ Session::get('success') }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="pagetitle">
    <h1>Reasons Of DelayTime</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">List Of Reasons</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="col-md-12" style="text-align: right;">

        <a href="{{ route('reasons.create') }}" class="btn btn-sm custom-btn">
            <i class="bi bi-plus-lg"></i>
        </a>

    </div>
</div>

<section class="section dashboard">
    <div class="row">

        {{-- Reason Card --}}
        <div class="col-md-12">
            <div class="card">

                <div class="card-body">
                    <div class="card-title">
                        <div class="col-md-6">
                            {{-- <label for="role">Select Department:</label> --}}
                            <select id="departments" name="departments" class="form-select">
                                <option value="0">All Departments</option>
                                @foreach($departments as $department)
                                <option value="{{$department->id}}">{{$department->name}}</option>
                                @endforeach
                                <!-- Add more options as needed -->
                            </select>
                        </div>
                    </div>
                    <div class="table-responsive custom_dt_table">
                        <table class="table w-100" id="ReasonTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Department</th>
                                    <th>Reasons</th>
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

    var table = $('#ReasonTable').DataTable({
        paging:false,
        searching:false,
        processing: true,
        serverSide: true,
        pageLength: 100,
        ajax: {
            url: "{{ route('reasons') }}",
            data : function(d){
                 d.departments = $('#departments').val();

             }
        },
        columns: [{
                data: 'id',
                name: 'id'
            },
            {
                data: 'department',
                name: 'department'
            },
            {
                data: 'reason',
                name: 'reason',
            },
            // {
            //     data: 'actions',
            //     name: 'actions',
            //     orderable: false,
            //     searchable: false
            // },
        ]
    });

    $('#departments').on('change', function() {
        table.ajax.reload(); // Redraw the DataTable with the new filter
    });


});


// Function for Delete Role
// function deleteRole(roleId) {

//     swal({
//             title: "Are you sure You want to Delete It ?",
//             icon: "warning",
//             buttons: true,
//             dangerMode: true,
//         })
//         .then((willDeleteRole) => {
//             if (willDeleteRole) {
//                 $.ajax({
//                     type: "POST",
//                     url: "{{ route('roles.destroy') }}",
//                     data: {
//                         "_token": "{{ csrf_token() }}",
//                         'id': roleId,
//                     },
//                     dataType: 'JSON',
//                     success: function(response) {
//                         if (response.success == 1) {
//                             toastr.success(response.message);
//                             $('#RoleTable').DataTable().ajax.reload();
//                         } else {
//                             swal(response.message, "", "error");
//                         }
//                     }
//                 });
//             } else {
//                 swal("Cancelled", "", "error");

//             }
//         });
// }
</script>

@endsection
