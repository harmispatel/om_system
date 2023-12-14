<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <!-- <link href="{{ asset('public/images/demo_logos/inj.png') }}" rel="icon"> -->
    @include('admin.layouts.admin-css')
    <style>
        /* Your regular styles here */

        #printableArea {
            /* Styles for the div you want to print */
            background-color: #fff;
            padding: 20px;
            border: 1px solid #000;
            margin: 20px;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            #printableArea, #printableArea * {
                visibility: visible;
            }
            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
        span.select2.select2-container.select2-container--classic{
        width: 100% !important;
    }
        #TouchV{
            color: red;
        }
        #validation{
            color: red;
        }
    </style>

   
</head>

<body>
    {{-- Navbar --}}
    @include('admin.layouts.admin-navbar')

    {{-- Sidebar --}}
    @include('admin.layouts.admin-sidebar')

    {{-- Main Content --}}
    <main id="main" class="main">
        @yield('content')
    </main>
    <!-- End #main -->

    {{-- Footer --}}
    @include('admin.layouts.admin-footer')

    {{-- Uplink --}}
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    {{-- Admin JS --}}
    @include('admin.layouts.admin-js')

    @yield('page-js')

</body>

</html>
