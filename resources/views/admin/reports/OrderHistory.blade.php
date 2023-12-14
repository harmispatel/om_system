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
        <h1>Reports</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('reports.orderhistory') }}">Order-history Report</a></li>
                        
                        <li class="breadcrumb-item active">Details</li>
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
                            <table class="table w-100" id="OrderHistoryTable">
                                <thead>
                                    <tr>
                                        <!--<th>Sr.No</th>-->
                                        <th>Order No</th>
                                        <th>In Switch Time/Name</th>
                                        <th>Out Switch Time/Name</th>
                                        <th>Department Name</th>
                                        <th>Duration</th>
                                        <!--<th>Action</th>-->
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

var table = $('#OrderHistoryTable').DataTable({
    searching: false,
    lengthChange: false, 
     paging: false,
    processing: true,
    serverSide: true,
    pageLength: 100,
    ajax: {
        url:"{{ route('reports.orderhistory.detail',$id) }}",
        data: function(d) {
           d.order_number = $('#order_number').val(); // Pass the order number to the server
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
            data: 'inswitch_time',
            name: 'inswitch_time',
        },
        {
            data: 'outswitch_time',
            name: 'outswitch_time'
        },
       
        {
            data: 'user_name',
            name: 'user_name'
        },
       
        {
            data: 'duration',
            name: 'duration',
        },
        // {
        //     data: 'actions',
        //     name: 'actions',
        //     orderable: false,
        //     searchable: false
            
        // }
    ]
});
});
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