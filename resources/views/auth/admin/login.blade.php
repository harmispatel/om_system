<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>Login Page</title>
    <!-- <link href="{{ asset('public/images/demo_logos/inj.png') }}" rel="icon"> -->
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('public/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">

    {{-- Toastr --}}
    <link href="{{ asset('public/assets/vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">

</head>

<body>

    <main>
        <div class="container">
            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
                            <div class="d-flex justify-content-center py-4">
                                <a href="#" class="logo d-flex align-items-center w-auto">
                                    <!-- {{-- <img src="{{ asset('public/images/demo_logos/inj.png') }}"> --}} -->
                                    <!-- {{-- <span class="d-none d-lg-block">ADMIN LOGIN</span> --}} -->
                                </a>
                            </div>

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Login to Your Account</h5>
                                        <p class="text-center small">Enter your Email & password to login</p>
                                    </div>
                                    <form class="row g-3" action="{{ route('admin.do.login') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                       
                                        <div class="col-12">
                                            <label for="email" class="form-label">Email</label>
                                            <div class="input-group">
                                                <input type="text" name="email" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}" id="email">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                @if($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('email') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <label for="password" class="form-label">Password</label>
                                            <div class="input-group">
                                                <input type="password" name="password" class="form-control {{ ($errors->has('password')) ? 'is-invalid' : '' }}" id="password">
                                                <span class="input-group-text" style="cursor: pointer;" onclick="ShowHidePassword()" id="passIcon"><i class="bi bi-eye-slash"></i></span>
                                                @if($errors->has('password'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('password') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                       
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- Vendor JS Files -->
    <script src="{{ asset('public/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    {{-- Jquery --}}
    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>

    {{-- Sweet Alert --}}
    <script src="{{ asset('public/assets/js/sweet-alert.js') }}"></script>

    {{-- Toastr --}}
    <script src="{{ asset('public/assets/vendor/toastr/js/toastr.min.js') }}"></script>

    <!-- Template Main JS File -->
    <script src="{{ asset('public/assets/js/main.js') }}"></script>

    {{-- Custom Script --}}
    <script type="text/javascript">

        // Error Message
        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        // Show & Hide Password
        function ShowHidePassword()
        {
            var currentType = $('#password').attr('type');
            if (currentType == 'password')
            {
                $('#password').attr('type', 'text');
                $('#passIcon').html('');
                $('#passIcon').append('<i class="bi bi-eye"></i>');
            }
            else
            {
                $('#password').attr('type', 'password');
                $('#passIcon').html('');
                $('#passIcon').append('<i class="bi bi-eye-slash"></i>');
            }
        }

    </script>

</body>

</html>
