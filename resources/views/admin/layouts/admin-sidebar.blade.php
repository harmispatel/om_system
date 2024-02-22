@php
    $currentRouteName = Route::currentRouteName();
    $user_dt = Auth::guard('admin')->user();
    $user_type = (isset($user_dt->user_type)) ? $user_dt->user_type : '';
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
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'order' ? '' : 'collapsed' }}" href="{{ route('order') }}">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Orders</span>
            </a>
        </li>


        {{-- Routes Only for Admin --}}
        @if ($user_type == 1)

            {{-- Task Management Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'task-management' || $currentRouteName == 'task-manage.list' ? '' : 'collapsed' }}" href="{{route('task-manage.list')}}">
                    <i class="fas fa-tasks"></i>
                    <span>Task Management</span>
                </a>
            </li>

            {{-- Reasons for Delay Time Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'reasons' ? '' : 'collapsed' }}" href="{{route('reasons')}}">
                    <i class="fa-solid fa-business-time"></i>
                    <span>Delay Time Reasons</span>
                </a>
            </li>
            {{-- Reasons for block Order --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'block-reasons' ? '' : 'collapsed' }}" href="{{route('block-reasons')}}">
                    <i class="fa-solid fa-business-time"></i>
                    <span>Reason For Block-Order</span>
                </a>
            </li>

            {{-- Types of Works Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'types_work' ? '' : 'collapsed' }}" href="{{ route('types_work') }}">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Types Of Works</span>
                </a>
            </li>

            {{-- Users Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'users' ? '' : 'collapsed' }}" href="{{ route('users') }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>

            {{-- Departments Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'roles' ? '' : 'collapsed' }}" href="{{ route('roles') }}">
                    <i class="fa-solid fa-network-wired"></i>
                    <span>Departments</span>
                </a>
            </li>

        @endif

        {{-- Reports Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ $currentRouteName == 'reports.order_history' || $currentRouteName == 'reports.typeofwork' ||  $currentRouteName == 'reports.department_pending_orders' || $currentRouteName == 'reports.performance' || Route::currentRouteName() == 'reports.order_history_details' ||  Route::currentRouteName() == 'reports.typesofworks_pending' || Route::currentRouteName() == 'orders.blocklist' || Route::currentRouteName() == 'reports.delayreason' ?  '' :'collapsed'}} " data-bs-target="#reports-nav"
            data-bs-toggle="collapse" href="#" aria-expanded="{{ Route::currentRouteName() == 'reports' ? 'true' : 'false'}}">
                <i class="fa fa-solid fa-file {{Route::currentRouteName() == 'reports' ? 'icon-tab':''}}"></i><span>Reports</span>
                <i class="bi bi-chevron-down ms-auto {{Route::currentRouteName() == 'reports' ? 'icon-tab':''}}"></i>

            </a>
            <ul id="reports-nav" class="nav-content sidebar-ul collapse {{ (Route::currentRouteName() == 'reports.order_history' || Route::currentRouteName() == 'reports.department_pending_orders' || $currentRouteName == 'reports.performance' || Route::currentRouteName() == 'reports.order_history_details' ||  Route::currentRouteName() == 'reports.typesofworks_pending' || Route::currentRouteName() == 'orders.blocklist' || Route::currentRouteName() == 'reports.delayreason') ? 'show':''}}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('reports.order_history')}}" class="{{ (Route::currentRouteName() == 'reports.order_history' || Route::currentRouteName() == 'reports.order_history_details') ? 'active': '' }}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.order_history' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Order History
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('reports.department_pending_orders')}}" class="{{Route::currentRouteName() == 'reports.department_pending_orders' ? 'active':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.department_pending_orders' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Department Pending Orders
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('reports.typesofworks_pending') }}" class="{{ Route::currentRouteName() == 'reports.typesofworks_pending' ? 'active':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.typesofworks_pending' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Types Of Works Pending
                        </span>
                    </a>
                </li>


                <li>
                    <a href="{{route('reports.performance')}}" class="{{Route::currentRouteName() == 'reports.performance' ? 'active':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.performance' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Department Performance
                        </span>
                    </a>
                </li>

                <li>
                    <a href="{{route('reports.delayreason')}}" class="{{Route::currentRouteName() == 'reports.delayreason' ? 'active':''}}">
                        <i class="bi bi-circle {{Route::currentRouteName() == 'reports.delayreason' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i><span>Delay-Reason Report
                        </span>
                    </a>
                </li>

                 <li>
                    <a class="{{Route::currentRouteName() == 'orders.blocklist' ? 'active':''}}" href="{{route('orders.blocklist')}}">
                         <i class="bi bi-circle {{Route::currentRouteName() == 'orders.blocklist' ? 'bi bi-circle-fill' : 'bi bi-circle'}}"></i>
                        <span>Blocked Orders</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Routes Only for Admin --}}
        @if($user_type == 1)
            {{-- Settings Nav --}}
            <li class="nav-item">
                <a class="nav-link {{ $currentRouteName == 'General.create' ? '' : 'collapsed' }}" href="{{route('General.create')}}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </a>
            </li>
        @endif

    </ul>
</aside>
