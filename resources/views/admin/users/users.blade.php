@php
    $role = Auth::guard('admin')->user()->user_type;
    $user_add = Spatie\Permission\Models\Permission::where('name','users.create')->first();

    $permissions = App\Models\RoleHasPermissions::where('role_id',$role)->pluck('permission_id');
    foreach ($permissions as $permission) {
        $permission_ids[] = $permission;
    }
@endphp

@extends('admin.layouts.admin-layout')

@section('title', 'Users')

@section('content')


<div class="pagetitle">
    <h1>Users</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Users</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4" style="text-align: right;">
            @if((in_array($user_add->id, $permission_ids)))
                <a href="{{ route('users.create') }}" class="btn btn-sm  custom-btn">
                    <i class="bi bi-plus-lg"></i>
                </a>
            @endif
        </div>
    </div>
</div>

 {{-- Users Section --}}
 <section class="section dashboard">
    <div class="row">

        {{-- Users Card --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                    </div>
                    <div class="table-responsive custom_dt_table">
                        <table class="table w-100" id="UsersTable">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile</th>
                                    <th>Department</th>
                                    <th>Status</th>
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


{{-- Custom Script --}}
@section('page-js')

    <script type="text/javascript">

            $(function() {
                var table = $('#UsersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    pageLength: 100,
                    ajax: "{{ route('users.load') }}",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'image',
                            name: 'image'
                        },
                        {
                            data: 'usertype',
                            name: 'usertype'

                        },
                        {
                            data: 'status',
                            name: 'status'

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

            // Function for change status of User
            function changeStatus(status, id) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('users.status') }}",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "status": status,
                        "id": id
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.success == 1) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    }
                })
            }

            // Function for Delete User
            function deleteUsers(userId) {
                swal({
                    title: "Are you sure You want to Delete It ?",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDeleteUsers) => {
                    if (willDeleteUsers) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('users.destroy') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                'id': userId,
                            },
                            dataType: 'JSON',
                            success: function(response) {
                                if (response.success == 1) {
                                    toastr.success(response.message);
                                    $('#UsersTable').DataTable().ajax.reload();
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
    </script>

@endsection
