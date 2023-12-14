@extends('admin.layouts.admin-layout')
@section('content')
@php
        $userDetails = auth()->guard('admin')->user();
        $userId = auth()->guard('admin')->user()->id;
        $userName = auth()->guard('admin')->user()->firstname." ".auth()->guard('admin')->user()->lastname;
        $userImage = auth()->guard('admin')->user()->image;
        $userType = auth()->guard('admin')->user()->user_type;
        $fatchTypeName= Spatie\Permission\Models\Role::where('id',$userType)->first();
        $userTypeName = $fatchTypeName->name;
        $userPhoneNo = auth()->guard('admin')->user()->phone;
        $userEmail = auth()->guard('admin')->user()->email;
        $user_edit = App\Models\Permission::where('name','users.edit')->first();
        $roles = App\Models\RoleHasPermissions::where('role_id',$userType)->pluck('permission_id');
            foreach ($roles as $key => $value) {
                   $val[] = $value;
              }

@endphp

<section class="section profile">
    <div class="row">
        <div class="col-xl-12">

            <div class="card">
             <div class="text-end p-3">
                @if(in_array($user_edit->id,$val))
                 <a href="{{route('users.edit', encrypt($userId))}}" class="btn btn-secondary btn-lg"><i class="fas fa-edit"></i></a>
                @endif
             </div>   
             <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                    @if($userImage)
                    <img src="{{ asset('public/images/uploads/user_images/'.$userImage) }}" alt="Profile"
                        class="rounded-circle">
                    @else
                    <img src="{{ asset('public/images/demo_images/profiles/profile1.jpg') }} " alt="Profile"
                        class="rounded-circle">
                    @endif

                    <h2>{{$userName}}</h2>
                    <h3>{{$userTypeName}}</h3>
                    <div class="social-links mt-2">
                        <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>

                <div class="container p-5">
                   <div class="row md-2">
                        <h5 class="card-title"> Profile Details </h5>
                   </div>
                    <div class="row md-2">
                        <div class="col-lg-3 col-md-4 label fw-bold">Full Name</div>
                        <div class="col-lg-8 col-md-8">{{$userName}}</div>
                    </div>

                    <div class="row md-2">
                        <div class="col-lg-3 col-md-4 label fw-bold">Country</div>
                        <div class="col-lg-9 col-md-8">India</div>
                    </div>

                    <div class="row md-2">
                        <div class="col-lg-3 col-md-4 label fw-bold">Address</div>
                        <div class="col-lg-9 col-md-8">A108 Adam Street, New York, NY 535022</div>
                    </div>

                    <div class="row md-2">
                        <div class="col-lg-3 col-md-4 label fw-bold">Phone</div>
                        <div class="col-lg-9 col-md-8">{{$userPhoneNo}}</div>
                    </div>

                    <div class="row md-2">
                        <div class="col-lg-3 col-md-4 label fw-bold">Email</div>
                        <div class="col-lg-9 col-md-8">{{$userEmail }}</div>
                    </div>
                </div>
            
            </div>

        </div>


    </div>
</section>

@endsection