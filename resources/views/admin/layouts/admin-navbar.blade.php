@php
    // Admin Details
    if (auth()->guard('admin')->user())
    {
        $userID = encrypt(auth()->guard('admin')->user()->id);
        $userName = auth()->guard('admin')->user()->firstname." ".auth()->guard('admin')->user()->lastname;
        $userImage = auth()->guard('admin')->user()->image;
        $userType = auth()->guard('admin')->user()->user_type;
        $typeName= Spatie\Permission\Models\Role::where('id',$userType)->first();
        $userTypeName = $typeName->name;
        $logo = "public/images/demo_images/logos/logo.png";
    }
    else
    {
        $userID = '';
        $userName = '';
        $userImage = '';
        $logo = "";
    }
@endphp

<header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between text-center">
        <!-- <a href="{{ route('admin.dashboard') }}" class="logo d-flex align-items-center justify-content-center">
            @if(!empty($logo))
                <img height="100px" width="50px" src="{{asset($logo)}}" alt="Logo">
            @else
                <span class="d-none d-lg-block">Logo Here</span>
            @endif
        </a> -->

        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div>

    <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
            <li class="nav-item dropdown pe-3">
                <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                    @if (!empty($userImage) || $userImage != null && file_exists('public/images/uploads/user_images/'.$userImage))
                        <img src="{{ asset('public/images/uploads/user_images/'.$userImage) }}" alt="Profile" class="rounded-circle">
                    @else
                        <img src="{{ asset('public/images/demo_images/profiles/profile1.jpg') }}" alt="Profile" class="rounded-circle">
                    @endif
                    <span class="d-none d-md-block dropdown-toggle ps-2">{{ $userName }}</span>
                </a>

                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                    <li class="dropdown-header">
                        <h6>{{ $userName }}</h6>
                        <span>{{ $userTypeName }}</span>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="{{route('users.profile')}}">
                            <i class="bi bi-person"></i>
                            <span>My Profile</span>
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>

                    <li>
                        <a href="{{ route('admin.logout') }}" class="dropdown-item d-flex align-items-center">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</header>
