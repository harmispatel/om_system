@php
$currentRouteName = Route::currentRouteName();
$user_dt = Auth::guard('admin')->user();
$role_id = $user_dt->user_type;
$permissions = App\Models\RoleHasPermissions::where('role_id', $role_id)->pluck('permission_id');

    foreach ($permissions as $permission) {
        $permission_ids[] = $permission;
    }

$role_permission = Spatie\Permission\Models\Permission::where('name', 'roles')->first();
$user_permission = Spatie\Permission\Models\Permission::where('name', 'users')->first();
$order_permission = Spatie\Permission\Models\Permission::where('name', 'order')->first();
$report_permission =  Spatie\Permission\Models\Permission::where('name', 'reports')->first();
$taskmanage_permission =  Spatie\Permission\Models\Permission::where('name', 'task_management')->first();
@endphp

<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        {{-- Dashboard Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'admin.dashboard' ? '' : 'collapsed' }}"
                href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>

        {{-- Orders Nav --}}
        @if (in_array($order_permission->id, $permission_ids))
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'order' ? '' : 'collapsed' }}" href="{{ route('order') }}">
                    <!-- <i class="bi bi-cart-fill"></i> -->
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Orders</span>
                </a>
            </li>
        @endif

        {{-- Reports Nav--}}
        @if(in_array($report_permission->id, $permission_ids))
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'reports.orderhistory' || $currentRouteName == 'reports.typeofwork' ||  $currentRouteName == 'reports.pending'|| $currentRouteName == 'reports.performance' ?  '' :'collapsed'}} " data-bs-target="#reports-nav" 
            data-bs-toggle="collapse" href="#" aria-expanded="{{ Route::currentRouteName() == 'reports' ? 'true' : 'false'}}">
                <i class="fa fa-solid fa-file {{Route::currentRouteName() == 'reports' ? 'icon-tab':''}}"></i><span>Reports</span>
                <i class="bi bi-chevron-down ms-auto {{Route::currentRouteName() == 'reports' ? 'icon-tab':''}}"></i>

            </a>
            <ul id="reports-nav" class="nav-content sidebar-ul collapse {{ Route::currentRouteName() == 'IACCounterReport' ? 'show':''}}" data-bs-parent="#sidebar-nav">

                <li>
                    <a href="{{route('reports.orderhistory')}}" class="{{Route::currentRouteName() == 'reports.orderhistory' ? 'active-link':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.orderhistory' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Order History
                        </span>
                    </a>
                </li>
                <!--<li>-->
                <!--    <a href="{{route('reports.typeofwork')}}" class="{{Route::currentRouteName() == 'reports.typeofwork' ? 'active-link':''}}">-->
                <!--        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.typeofwork' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Type Of Work-->
                <!--        </span>-->
                <!--    </a>-->
                <!--</li>-->
                <li>
                    <a href="{{route('reports.pending')}}" class="{{Route::currentRouteName() == 'reports.pending' ? 'active-link':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.pending' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Department Pending Orders
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{route('reports.performance')}}" class="{{Route::currentRouteName() == 'reports.performance' ? 'active-link':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.performance' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Department Performance
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        @endif


        {{-- User Nav --}}
        @if (in_array($role_permission->id, $permission_ids))
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'roles' ? '' : 'collapsed' }}" href="{{ route('roles') }}">
                <!-- <i class="bi bi-cart-fill"></i> -->
                <i class="fa-solid fa-network-wired"></i>
                <span>Departments</span>
            </a>
        </li>
        @endif
        @if (in_array($role_permission->id, $permission_ids))
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'users' ? '' : 'collapsed' }}" href="{{ route('users') }}">
                <!-- <i class="bi bi-cart-fill"></i> -->
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
        </li>
        @endif
        <!-- <li class="nav-item">
            <a class="nav-link {{ Route::currentRouteName() != 'roles' && Route::currentRouteName() != 'roles.create' && Route::currentRouteName() != 'roles.edit' && Route::currentRouteName() != 'users' && Route::currentRouteName() != 'users.create' && Route::currentRouteName() != 'users.edit' ? 'collapsed' : '' }} {{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'active-tab' : '' }}"
                data-bs-target="#users-nav" data-bs-toggle="collapse" href="#"
                aria-expanded="{{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'true' : 'false' }}">
                <i
                    class="fa-solid fa-users {{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'icon-tab' : '' }}"></i><span>Users</span><i
                    class="bi bi-chevron-down ms-auto {{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'icon-tab' : '' }}"></i>
            </a>
            <ul id="users-nav"
                class="nav-content sidebar-ul collapse {{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' || Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'show' : '' }}"
                data-bs-parent="#sidebar-nav">
                @if (in_array($role_permission->id, $permission_ids))
                <li>
                    <a href="{{ route('roles') }}"
                        class="{{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' ? 'active-link' : '' }}">
                        <i
                            class="{{ Route::currentRouteName() == 'roles' || Route::currentRouteName() == 'roles.create' || Route::currentRouteName() == 'roles.edit' ? 'bi bi-circle-fill' : 'bi bi-circle' }}"></i><span>Departments
                        </span>
                    </a>
                </li>
                @endif
                @if (in_array($user_permission->id, $permission_ids))
                    <li>
                        <a href="{{ route('users') }}"
                            class="{{ Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users.edit' ? 'active-link' : '' }}">
                            <i
                                class="{{ Route::currentRouteName() == 'users' || Route::currentRouteName() == 'users.create' || Route::currentRouteName() == 'users .edit' ? 'bi bi-circle-fill' : 'bi bi-circle' }}"></i><span>Users</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li> -->
        
        @if (in_array($taskmanage_permission->id, $permission_ids))
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'task-management' ? '' : 'collapsed' }}" href="{{route('task-manage.list')}}">
                <!-- <i class="bi bi-cart-fill"></i> -->
                <i class="fas fa-tasks"></i>
                <span>Task Management</span>
            </a>
        </li>
        @endif
        
        @if($user_dt->id == 1)
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'types_work' ? '' : 'collapsed' }}" href="{{ route('types_work') }}">
                <!-- <i class="bi bi-cart-fill"></i> -->
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Types Of Works</span>
            </a>
        </li>
        
        <li class="nav-item">
           <a class="nav-link {{ $currentRouteName == 'types_work' ? '' : 'collapsed' }}" href="{{route('General.create')}}">
               <!-- <i class="bi bi-cart-fill"></i> -->
               <i class="fa-solid fa-business-time"></i>
               <span>General Setting</span>
           </a>
       </li>
       @endif
        </li>
    </ul>
</aside>
