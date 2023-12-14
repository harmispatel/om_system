{{-- @php
    $role = Auth::guard('admin')->user()->user_type;
    $role_add = Spatie\Permission\Models\Permission::where('name','roles.create')->first();

    $permissions = App\Models\RoleHasPermissions::where('role_id',$role)->pluck('permission_id');
    foreach ($permissions as $permission) {
        $permission_ids[] = $permission;
    }
@endphp --}}

@extends('admin.layouts.admin-layout')

@section('title', 'Roles')

@section('content')
  @if(Session::has('success'))
  <div class="alert alert-info alert-dismissible fade show" role="alert">
  <strong>{{ Session::get('success') }}</strong>
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

   @endif
    <div class="pagetitle">
        <h1>Types Of Works</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Types Of Works</li>
                    </ol>
                </nav>
            </div>
           
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
                                        <th>Types Works</th>
                                        <th>Order Value</th>
                                        <th>Works Hours</th>
                                        <th>Action</th>
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
                ajax: "{{route('types_work')}}",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'types_of_works',
                        name: 'types_of_works'
                    },
                    {
                        data: 'order_value',
                        name: 'order_value',
                    },
                    {
                        data: 'works_time',
                        name: 'works_time',
                    },
                    {
                        data:'actions',
                        name:'actions'
                    }
                   
                ]
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
        //                     url: "",
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