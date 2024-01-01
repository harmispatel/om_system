<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="margin:0;height:100%;">
    <div style="display: flex;align-items:center;justify-content:center;">
        <div class="receipt" style="position: relative;width: 148mm; height: 210mm;">
            <div class="content" style="padding:30px">
                <div class="div">
                    <table style="width:100%; border:none;">
                        <tr>
                            <td style="width: 100%;">
                                <table style="width:100%; border:none;font-size:10px;border-collapse:collapse;">
                                    <tr>
                                        <td colspan="2">
                                            <table
                                                style="width:100%; border:none;font-size:14px;border-collapse:collapse;">
                                                <tr>
                                                    <td style="padding: 0 10px;width:40%;">
                                                        <table
                                                            style="width:100%; border:none;font-size:14px;border-collapse:collapse;">
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Type of Work</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$typeofwork['types_of_works'] }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Order Type</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{ ($order_details->SelectOrder == 1) ? 'Repeat Order' : 'New Order' }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Customer Mobile</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$order_details->mobile }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Who's Metal ?</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$order_details->gold }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border-bottom: 1px solid #000;padding:5px">
                                                                    <b>Charges</b></td>
                                                                <td
                                                                    style="border-bottom: 1px solid #000;padding:5px;text-align:right;">
                                                                    {{$order_details->charges }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Metal Weight</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$order_details->metalwt }} WT</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="padding: 0 10px;width:40%;">
                                                        <table
                                                            style="width:100%; border:none;font-size:14px;border-collapse:collapse;">
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Order Number</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{ date('d-m-Y h:i:s', strtotime($order_details->created_at)) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Customer Name</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$order_details->name }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Touch</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{ number_format($order_details->touch, 2) }} %</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Metal</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{$order_details->metal }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td style="border-bottom: 1px solid #000;padding:5px">
                                                                    <b>Advance</b></td>
                                                                <td
                                                                    style="border-bottom: 1px solid #000;padding:5px;text-align:right;">
                                                                    {{$order_details->advance }}</td>
                                                            </tr>
                                                            <tr>
                                                                <!-- <td style="border-bottom: 1px solid #000;padding:10px 5px"><b>Delivery Date</b></td> -->
                                                                <td colspan="2"
                                                                    style="border-bottom: 1px solid #000;padding:5px;">
                                                                    {{ date('d-m-Y h:i:s', strtotime($order_details->deliverydate)) }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td style="width:20%;vertical-align: top;">
                                                        <table
                                                            style="width:100%; border:none;font-size:14px;border-collapse:collapse;">
                                                            <tr>
                                                                <td>
                                                                    <div class="qr-code">
                                                                        @if(!empty($order_details->Qrphoto) &&
                                                                        file_exists('public/images/qrcodes/'.$order_details->Qrphoto))
                                                                        <img src="{{ asset('public/images/qrcodes/'.$order_details->Qrphoto) }}"
                                                                            width="100%" style="height:auto;">
                                                                        @endif
                                                                    </div>
                                                                    <div style="text-align:center;">
                                                                        <label class="fw-bold">
                                                                           <span><b>{{$order_details->orderno }}</b></span>
                                                                            <h3 style="margin:0;"><span class="fw-bold"> {{ $order_details->handleby }}</span>
                                                                            </h3>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 80%;padding:10px;vertical-align:baseline;">
                                            <table style="width:100%; border:none;">
                                                @if(isset(($order_images[0])))
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <img src="{{ asset('public/orderimages/'.$order_images[0]['orderimage']) }}"
                                                            style="width:100%;height:535px;">
                                                    </td>
                                                </tr>
                                                @endif
                                            </table>
                                        </td>
                                        <td style="width: 20%;padding:10px;vertical-align:top;">
                                            <table
                                                style="width:100%; border:none;font-size:10px;border-collapse:collapse;">
                                                <tr>
                                                    <td style="padding:10px 5px">1 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">2 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">3 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">4 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">5 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">6 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">7 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">8 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:10px 5px">9 <span
                                                            style="width: 100%;border-bottom:1px solid #000;padding:5px;display:block;"></span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>

    @if(count($order_images) > 0)
    @foreach ($order_images as $key => $order_image)
    @if($key != 0)
    <div class="receipt" style="position: relative;margin-top:80px">
        <div class="content" style="padding: 50px 30px">
            <div class="div">
                <table style="width:100%; border:none;">
                    <tr>
                        <td style="text-align: center;">
                            <img src="{{ asset('public/orderimages/'.$order_image['orderimage']) }}"
                                style="width:70%;height:500px;">
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    @endif
    @endforeach
    @endif

    <script src="{{ asset('public/assets/js/jquery.min.js') }}"></script>
    <script type="text/javascript">
    $(document).ready(function () {
        window.print();

        window.onafterprint = function() {
            // Close the tab (or perform any other desired action)
            // setTimeout(() => {
            //     window.close();
            // }, 1200);
        };
    });
    </script>

</body>

</html>
