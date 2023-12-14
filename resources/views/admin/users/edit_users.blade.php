@extends('admin.layouts.admin-layout')

@section('title', 'New Users')

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>Users</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item "><a href="{{ route('users') }}">Users</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>


    {{-- New User add Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <form action="{{ route('users.update') }}" method="POST" enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <div class="form_box">
                                <div class="form_box_inr">
                                    <div class="box_title">
                                        <h2>User Details</h2>
                                    </div>
                                    <div class="form_box_info">
                                        <div class="row">
                                            <input type="hidden" name="id" id="id" value="{{encrypt($data->id)}}">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="firstname" class="form-label">First Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="firstname" value="{{isset($data->firstname) ? $data->firstname : old('firstname')}}" id="firstname" class="form-control {{ $errors->has('firstname') ? 'is-invalid' : '' }}" placeholder="Enter First Name">
                                                    @if ($errors->has('firstname'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('firstname') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for = "lastname" class="form-label">Last Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="lastname" value="{{isset($data->lastname) ? $data->lastname : old('lastname')}}" id="lastname" class="form-control {{ $errors->has('lastname') ? 'is-invalid' : '' }}" placeholder="Enter Last Name">
                                                    @if ($errors->has('lastname'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('lastname') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" name="email" value="{{isset($data->email) ? $data->email : old('email')}}" id="email" class="form-control {{$errors->has('email') ? 'is-invalid' : ''}}" placeholder="Enter Email">
                                                    @if ($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('email') }}
                                                    </div>
                                                @endif
                                                
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="phone" class="form-label">Mobile</label>
                                                    <input type="number" name="phone" value="{{isset($data->phone) ? $data->phone : old('phone')}}" id="phone" class="form-control {{$errors->has('phone') ? 'is-invalid' : ''}}" placeholder="Enter Mobile Number">
                                                    @if ($errors->has('phone'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('phone') }}
                                                        </div>
                                                    @endif

                                                </div>

                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">Password</label>
                                                    <input type="password" name="password" id="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" placeholder="Enter Password">
                                                    @if ($errors->has('password'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('password') }}
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control {{ $errors->has('confirm_password') ? 'is-invalid' : ''}}" placeholder="Enter Confirm Password">
                                                    @if ($errors->has('confirm_password'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('confirm_password') }}
                                                        </div>
                                                    @endif
                                                </div>

                                            </div>


                                            <div class="col-md-6 mb-3">
                                                <label for="user_type" class="form-label">Role <span class="text-danger">*</span></label>
                                                <select name="user_type" id="user_type" name="user_type" class="form-select">
                                                    @foreach ($roles as $role)
                                                    <option value="{{$role->id}}"{{$role->id == $data->user_type ? 'selected' : ''}}>{{ $role->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!--<div class="col-md-6 mb-3">-->
                                            <!--    <label for="user_type" class="form-label">Order-Delete Permission<span class="text-danger"></span></label>-->
                                            <!--        <div class="form-check">-->
                                            <!--            <input class="form-check-input" type="checkbox" value="1" id="flexCheckChecked" name="delete_order">-->
                                            <!--            <label class="form-check-label" for="flexCheckChecked">-->
                                            <!--                yes-->
                                            <!--            </label>-->
                                            <!--        </div>-->
                                            <!--    </div>-->
                                           </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form_box_inr">
                                    <div class="box_title">
                                        <h2>User Image</h2>
                                    </div>
                                    <div class="form_box_info">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form_group">
                                                    <label for="image" class="form-label">Profile Image</label>
                                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                                                    @if ($errors->has('image'))
                                                        <div class="invalid-feedback">
                                                            {{ $errors->first('image') }}
                                                        </div>
                                                    @endif
                                                    <div class="mt-2">
                                                        @if(isset($data->image) && !empty($data->image) && file_exists('public/images/uploads/user_images/'.$data->image))
                                                            <img src="{{ asset('public/images/uploads/user_images/' . $data->image) }}" alt="" width="100" height="100">
                                                        @else
                                                            <img src="{{ asset("public/images/demo_images/not-found/not-found4.png") }}" alt="" width="100" height="100">
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn form_button">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection
