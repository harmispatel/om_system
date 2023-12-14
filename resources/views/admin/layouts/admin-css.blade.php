<!-- Google Fonts -->
<link href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- jquery -->

<!-- Vendor CSS Files -->
<link href="{{ asset('public/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

{{-- Toastr --}}
<link href="{{ asset('public/assets/vendor/toastr/css/toastr.min.css') }}" rel="stylesheet">

{{-- font Awesome --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"/>

{{-- handleby --}}
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

{{-- Select 2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Template Main CSS File -->
<link href="{{ asset('public/assets/css/style.css') }}" rel="stylesheet">
<link href="{{ asset('public/assets/css/custom.css') }}" rel="stylesheet">

{{-- sweetAlert 2 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">

<link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

<style>
 #printableArea{
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
    /* Toggle Status Button */
    .form-check-input:checked{
        background-color: #198754!important;
        border-color: #198754!important;
    }
    .form-check-input{
        background-color: #e47b86!important;
        border-color: #e47b86!important;
    }
    .form-switch .form-check-input
    {
        width: 45px!important;
        height: 25px!important;
    }

    /* AutoComplete */
    .pac-container {
        z-index: 9999;
    }
      /* select 2 css */
      /* .select2 {
        width: 100%!important;
    } */
    .select2-selection{
        min-height: 37px!important;
    }
    .select2-container--default .select2-selection--multiple{
        border: 1px solid #ced4da!important;
    }
</style>
