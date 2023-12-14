<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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

</head>
<body>
<div class="container border p-2">
    <div class="text-end m-2" >
        <a id="printButton" href="{{route('order.print',$orderdetail->orderno)}}" target="_blank" class="btn btn-sm btn-warning rounded-circle" text="end" title="Print"><i
                class="fa fa-print" aria-hidden="true"></i></a>
    </div>
<div class="row">

    <div class="">
        <img alt="image" src="{{ asset('public/images/qrcodes/'. $orderdetail->Qrphoto)}}" class="">
    </div>
    <div class="col-lg-12 col-md-12 text-center">
        <h5 class="card-title"> Customer Order </h5>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <table class="table">
            <tr>
                <td>Order</td>

                <td> {{
                        $orderdetail->SelectOrder == 0
                            ? "New Order"
                            : "Repeat Order"
                    }}
                </td>
            </tr>
            <tr>
                <td>Counter Name</td>

                <td> @if(isset($orderdetail->counter_id))
                    {{$typesofworkId->types_of_works}}
                    @else
                    <p>-</p>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Order No</td>
                <td>{{ $orderdetail->orderno }}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td> {{ $orderdetail->name }} </td>
            </tr>
            <tr>
                <td>Mobile</td>
                <td> {{ $orderdetail->mobile }}</td>
            </tr>
            <tr>
                <td>Who's Metal</td>
                <td> {{ $orderdetail->gold }}</td>
            </tr>
            <tr>
                <td>Metal</td>
                <td>{{$orderdetail->metal}}</td>
            </tr>

        </table>
    </div>




    <div class="col-lg-6">
        <table class="table">

            <tr>
                <td>Touch</td>
                <td> {{ number_format($orderdetail->touch,2) }}</td>
            </tr>
            <tr>
                <td>Charges</td>
                <td>{{ $orderdetail->charges }}</td>
            </tr>
            <tr>
                <td>Advance</td>
                <td>{{ $orderdetail->advance }}</td>
            </tr>
            <tr>
                <td>Metal Weight</td>
                <td>{{ $orderdetail->metalwt }}</td>
            </tr>
            <tr>
                <td>Delivery Date</td>
                <td>{{ $orderdetail->deliverydate }}</td>
            </tr>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12 p-4">
        <table class="table border border-info table-light mt-5">
            <!-- <thead>
                    <tr>
                        <th colspan="2" class="text-center">Title</th>
                    </tr>
                </thead> -->
            <tbody class="text-center">
                <tr>
                    <td scope="row">
                        @if(isset($orderimages))
                        @foreach ($orderimages as $value)

                        <img alt="image" class="w-50"
                            src="{{ asset('public/orderimages/'. $value->orderimage)}}">

                        @endforeach
                        @else

                        <img src="{{asset('public/images/demo_images/not-found/not-found4.png')}}"
                            height="600px" width="450px">
                        @endif
                    </td>

                </tr>
            </tbody>
        </table>
    </div>
    <div class="row m-3 p-4">
        <div class="col-lg-12 text-end">
            <label class="fw-bold">Handle By : <span class="fw-bold">{{ $orderdetail->handleby
                        }}</span></label>

        </div>
    </div>
</div>

</div>
<script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript">

       

    </script>
</body>
</html>          