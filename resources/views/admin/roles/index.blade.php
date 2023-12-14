@php
    $role = Auth::guard('admin')->user()->user_type;
    $role_add = Spatie\Permission\Models\Permission::where('name','roles.create')->first();

    $permissions = App\Models\RoleHasPermissions::where('role_id',$role)->pluck('permission_id');
    foreach ($permissions as $permission) {
        $permission_ids[] = $permission;
    }
@endphp

@extends('admin.layouts.admin-layout')

@section('title', 'Departments')

@section('content')
  @if(Session::has('success'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
        <strong>{{ Session::get('success') }}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
   @endif
    <div class="pagetitle">
        <h1>Departments</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Departments</li>
                    </ol>
                </nav>
            </div>
            <!-- <div class="col-md-4" style="text-align: right;">
                @if (in_array($role_add->id, $permission_ids))
                    <a href="{{ route('roles.create') }}" class="btn btn-sm custom-btn">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                @else
                    <a href="{{ route('roles.create') }}" class="btn btn-sm custom-btn disabled">
                        <i class="bi bi-plus-lg"></i>
                    </a>
                @endif
            </div> -->
        </div>
    </div>

    {{-- Roles Section --}}
    <section class="section dashboard">
        <div class="row">

            {{-- Roles Card --}}
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                        </div>
                        <div class="table-responsive custom_dt_table">
                            <table class="table w-100" id="RoleTable">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Name</th>
                                        <th>Switches</th>
                                        <!-- <th>Working Hours</th>
                                        <th>Actions</th> -->
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

            var table = $('#RoleTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 100,
                ajax: "{{ route('roles') }}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'switches',
                        name: 'switches',
                    },
                    // {
                    //     data: 'actions',
                    //     name: 'actions',
                    //     orderable: false,
                    //     searchable: false
                    // },
                ]
            });

        });


        // Function for Delete Role
        function deleteRole(roleId) {

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
                            url: "{{ route('roles.destroy') }}",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                'id': roleId,
                            },
                            dataType: 'JSON',
                            success: function(response) {
                                if (response.success == 1) {
                                    toastr.success(response.message);
                                    $('#RoleTable').DataTable().ajax.reload();
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