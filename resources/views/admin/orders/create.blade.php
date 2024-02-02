@php
    $image_errors = [];
    $image_errors = $errors->toArray();
    if(isset($image_errors['counter_id'])){
        unset($image_errors['counter_id']);
    }
    if(isset($image_errors['name'])){
        unset($image_errors['name']);
    }
    if(isset($image_errors['mobile'])){
        unset($image_errors['mobile']);
    }
    if(isset($image_errors['SelectOrder'])){
        unset($image_errors['SelectOrder']);
    }
    if(isset($image_errors['touch'])){
        unset($image_errors['touch']);
    }
    if(isset($image_errors['gold'])){
        unset($image_errors['gold']);
    }
    if(isset($image_errors['metal'])){
        unset($image_errors['metal']);
    }
    if(isset($image_errors['handleby'])){
        unset($image_errors['handleby']);
    }

@endphp

@extends('admin.layouts.admin-layout')

@section('title', 'Orders')

@section('content')


{{-- Page Title --}}
<div class="pagetitle">
    <h1>Orders</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Orders</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

{{-- Orders Section --}}
<section class="section dashboard">


    <div class="col-md-12">
        <div class="card">

                <form class="form" id="form" action="{{ route('orders.store') }}" method="POST"
                    enctype="multipart/form-data">

                    <div class="card-body">
                        @csrf
                        <div class="form_box">
                            <div class="form_box_inr">
                                <div class="box_title">
                                    <h2>Orders Details</h2>
                                </div>
                                <div class="form_box_info">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="firstname" class="form-label">Types Of Work<span
                                                        class="text-danger">*</span></label>
                                                <select type="text" name="counter_id" id="select-box"
                                                    class="form-control {{ $errors->has('counter_id') ? 'is-invalid' : '' }}">
                                                    <option value="">--select value--</option>
                                                    @foreach ($counters as $counter)
                                                        <option value="{{ $counter->id }}"
                                                            {{ old('counter_id') == $counter->id ? 'selected' : '' }}>
                                                            {{ $counter->types_of_works }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('counter_id'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('counter_id') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="form-group">
                                                <label for="orderno" class="form-label">Order No</label>
                                                <input type="text" name="orderno" id="result-field" class="form-control"
                                                    value="{{ old('orderno') }}" required readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-2 mt-2">
                                            <input type="hidden" id="Qrphotoname" name="Qrphoto"
                                                value="{{ old('Qrphoto') }}">
                                            <div class="form-group" id="qrCodeImage" alt="QR Code" style="display: none">

                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="SelectOrder" class="form-label">Select Order<span
                                                    class="text-danger">*</span></label>
                                            <select type="text" name="SelectOrder" id="SelectOrder"
                                                class="form-control  {{ $errors->has('SelectOrder') ? 'is-invalid' : '' }}"
                                                value="{{ old('SelectOrder') }}">
                                                <option value="">--select value--</option>
                                                <option value="0"
                                                    {{ old('SelectOrder') == '0' ? 'selected' : '' }}>NewOrder</option>
                                                <option value="1" {{ old('SelectOrder') == '1' ? 'selected' : '' }}>
                                                    RepeatOrder</option>
                                            </select>
                                            @if ($errors->has('SelectOrder'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('SelectOrder') }}
                                                </div>
                                            @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="Mobile" class="form-label">Mobile No<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="mobile" id="mobile" maxlength="10"
                                                    class="form-control {{ $errors->has('mobile') ? 'is-invalid' : '' }}"
                                                    value="{{ old('mobile') }}">
                                                <div id="validation"></div>
                                                @if ($errors->has('mobile'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('mobile') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="lastname" class="form-label">Name<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name"
                                                    class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                                                    value="{{ old('name') }}">
                                                @if ($errors->has('name'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('name') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                         <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="gold" class="form-label">Metal<span
                                                        class="text-danger">*</span></label>
                                                <select type="text" name="metal" id="metal_gold"
                                                    value="{{ old('metal') }}"
                                                    class="form-control {{ $errors->has('metal') ? 'is-invalid' : '' }}">
                                                    <option value="">--select value--</option>
                                                    <option value="gold" {{ old('metal') == 'gold' ? 'selected' : '' }}>
                                                        gold</option>
                                                    <option value="silver"
                                                        {{ old('metal') == 'silver' ? 'selected' : '' }}>silver
                                                    </option>
                                                    <option value="immetation"
                                                        {{ old('metal') == 'immetation' ? 'selected' : '' }}>
                                                        immetation</option>
                                                </select>
                                                @if ($errors->has('metal'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('metal') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="gold" class="form-label">Who's Metal?<span
                                                        class="text-danger">*</span></label>
                                                <select type="text" name="gold" id="gold"
                                                    class="form-control  {{ $errors->has('gold') ? 'is-invalid' : '' }}"
                                                    value="{{ old('gold') }}">
                                                    <option value="">--select value--</option>
                                                    <option value="customer"
                                                        {{ old('gold') == 'customer' ? 'selected' : '' }}>customer</option>
                                                    <option value="IJPL" {{ old('gold') == 'IJPL' ? 'selected' : '' }}>
                                                        IJPL</option>
                                                </select>
                                                @if ($errors->has('gold'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('gold') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                          <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="touch" class="form-label">Touch<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="touch" id="touch" maxlength="5"
                                                    value="{{ old('touch') }}"
                                                    class="form-control {{ $errors->has('touch') ? 'is-invalid' : '' }}">
                                                <div id="TouchV"></div>
                                                @if ($errors->has('touch'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('touch') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="image" class="form-label">Photo<span
                                                        class="text-danger">*</span></label>
                                                <input type="file" name="orderimage[]" id="orderimage" onchange="validateFileType()"
                                                    class="form-control {{ (count($image_errors) > 0) ? 'is-invalid' : '' }}"  multiple>
                                                @if(count($image_errors))
                                                @foreach ($image_errors as $image_error)
                                                    @if(count($image_error) > 0)
                                                        @foreach ($image_error as $error_val)
                                                        <div class="invalid-feedback">
                                                            {{ $error_val }}
                                                        </div>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="charges" class="form-label">Charges<span></span></label>
                                                <input type="text" name="charges" id="charges"
                                                    value="{{ old('charges') }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="advance" class="form-label">Advance<span></span></label>
                                                <input type="text" name="advance" id="advance"
                                                    value="{{ old('advance') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="metalwt" class="form-label">Metal
                                                    Weight<span></span></label>
                                                <input type="text" name="metalwt" id="metalwt"
                                                    value="{{ old('metalwt') }}" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group">
                                                <label for="date" class="form-label">Delivery Date</label>
                                                <input type="text" name="deliverydate" id="deliverydate"
                                                    value="{{ old('deliverydate') }}" class="form-control" required
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="form-group ui-widget">
                                                <label for="handleby" class="form-label">Handle By<span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="handleby" placeholder="search" id="handleby"
                                                class="form-control">

                                                {{-- <select id="search-results" style="display: none;"
                                                    class="form-control"></select> --}}
                                                @if ($errors->has('handleby'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('handleby') }}
                                                    </div>
                                                @endif
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
            <div id="responseMessage"></div>
        </div>
    </div>
</section>



@endsection

{{-- Custom JS --}}
@section('page-js')


    <script type="text/javascript">
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            timeOut: 10000
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        // @if (Session::has('error'))
        //     toastr.error('{{ Session::get('error') }}')
        // @endif
    </script>

    <script>
         var showAlert = true;
        document.getElementById('form').addEventListener('submit', function(event) {
            var inputField = document.getElementById('charges').value;
            var inputField1 = document.getElementById('advance').value;
            var inputField2 = document.getElementById('metalwt').value;
            if (inputField === '' && showAlert || inputField1 === '' && showAlert || inputField2 === '' && showAlert) {
                alert('Input field cannot be blank!');
                showAlert = false; // Set showAlert to false after showing the alert once
                event.preventDefault(); // Prevent form submission when input is blank
            }
        });




        //Counter_id
        $(document).ready(function() {

            $('#select-box').change(function() {

                var selectedValue = $(this).val();

                $.ajax({
                    url: '{{ route('getdata') }}',
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: selectedValue
                    },
                    success: function(response) {

                        if (!response) {
                            $('#deliverydate').val('');
                            $('#result-field').val('');
                            $('#Qrphotoname').val('');
                            $('#qrCodeImage').hide();
                        } else {
                            $('#deliverydate').val(response.DeliveryDate);
                            $('#result-field').val(response.orderno);
                            var qrCodeImageUrl = "{{ asset('public/images/qrcodes') }}" + '/' +
                                response.qrcode_name;

                            $('#qrCodeImage').html('');
                            $('#qrCodeImage').append("<img  src='" + qrCodeImageUrl +
                                "' width='140'>");
                            $('#Qrphotoname').val(response.qrcode_name);
                            $('#qrCodeImage').show();
                        }

                    }
                });

            });
        });

        //name

        $(document).ready(function() {
            // Restrict input to numeric values only
            $('#name').on('input', function() {
                var inputValue = $(this).val();
                var sanitizedValue = inputValue.replace(/[^a-zA-Z ]/g, ''); // Remove non-numeric characters

                $(this).val(sanitizedValue);
            });
        });


        //mobile automiatic show name

        $('#mobile').change(function() {
            var mobileNumber = $(this).val();

            $.ajax({
                url: '{{ route('mobileSetName') }}',
                method: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "mobile": mobileNumber
                },
                success: function(response) {
                    if (response) {

                        $('#name').val(response.customerName);

                    } else {
                        $('#name').val('');
                    }
                }

            })
        });

        //Mobile
        $(document).ready(function() {
            // Restrict input to numeric values only
            $('#mobile').on('input', function() {
                var mobileNumber = $(this).val();
                var sanitizedValue = mobileNumber.replace(/[^0-9]/g, ''); // Remove non-numeric characters
                $(this).val(sanitizedValue);
            });

            $('#mobile').on('focusout', function() {
                toastr.clear();
                var mobile = $(this).val();
                var validationMessage = $('#validation');

                if (mobile.length == 0) {
                    $(this).toggleClass("is-invalid");
                    validationMessage.text("Mobile field is required");
                } else if (mobile.length < 10) {
                    $(this).toggleClass("is-invalid");
                    validationMessage.text("Mobile No. have must atleast 10 digits!");
                } else {
                    $(this).removeClass("is-invalid");
                    validationMessage.text("");
                }
            });
        });


        // Touch
        var addedDot = false;

        $('#touch').on('input', function(e) {
            var touch = $(this).val();
            var sanitizedValue = touch.replace(/[^0-9.]/g, '');

            // If there's no dot and two digits are entered, add a dot
            if (!addedDot && touch.length === 2 && touch.charAt(1) !== '.') {
                $(this).val(touch + ".");
                addedDot = true; // Set addedDot to true once a dot is added
            } else {
                // Remove non-numeric characters except for the first dot
                var match = sanitizedValue.match(/^(\d*\.?\d{0,3})$/);
                if (match) {
                    sanitizedValue = match[1];
                } else {
                    // If the input doesn't match the pattern, remove the last character
                    sanitizedValue = sanitizedValue.slice(0, -1);
                }

                // Update the input field with the sanitized value
                $(this).val(sanitizedValue);
            }
        });


        $('#touch').on('change', function(e) {
            var touch = $(this).val();

            if (touch.length === 1 && touch.charAt(0) === '.') {
                // If the user enters a single point, clear it
                $(this).val('');
                addedDot = false;
            } else if (touch.length === 1) {
                // If the user enters a single point, clear it
                $(this).val('');
                addedDot = false;
            } else if (touch.length === 2 && touch.charAt(1) === '.') {
                // If the user enters a single point, clear it
                $(this).val('');
                addedDot = false;
            } else if (touch.length === 2 && !addedDot) {
                // If two digits are entered and no dot is present, add ".00"
                $(this).val(touch + ".00");
                addedDot = true;
            } else if (touch.length === 1 + '.' && !addedDot) {
                $(this).val(touch + "");
                addedDot = true;
            } else if (touch.length === 3 && touch.charAt(2) === '.') {
                // If user entered 2 digits and a dot, add "00" after the dot
                $(this).val(touch + "00");
                addedDot = true;
            } else if (touch.length === 4 && touch.charAt(2) === '.' && touch.charAt(3) !== '0') {
                // If user entered 2 digits, a dot, and 1 digit (not 0), add "0" after the dot
                $(this).val(touch + "0");
                addedDot = true;
            } else if (touch.length === 4) {
                // If four digits are entered, add the value to 0
                $(this).val(touch.substring(0, 3) + "0");
                addedDot = true;
            }
        });

        $('#touch').on('keydown', function(e) {
            if (e.key === 'Backspace') {
                var touch = $(this).val();
                if (touch.charAt(touch.length - 1) === '.') {
                    // If backspace is pressed and the last character is a dot, remove the dot
                    $(this).val(touch.substring(0, touch.length - 1));
                    addedDot = false;
                }
            }
        });

        $('#touch').on('focusout', function() {
            addedDot = false;
            $('#touch').on('focusout', function() {
                toastr.clear();
                var mobile = $(this).val();
                var validationMessage = $('#TouchV');

                if (mobile.length == 0) {
                    $(this).toggleClass("is-invalid");
                    validationMessage.text("Touch field is required");
                } else if (mobile.length < 5) {
                    $(this).toggleClass("is-invalid");
                    validationMessage.text("Touch have must atleast 2 digits!");
                } else {
                    $(this).removeClass("is-invalid");
                    validationMessage.text("");
                }
            });
        });

        $('#touch').on('blur', function() {
            var touch = $(this).val();
            if (touch.length === 3 && touch.charAt(2) === '.') {
                // If user entered 2 digits and a dot, add "00" after the dot
                $(this).val(touch + "00");
                addedDot = true;
            }
        });

@if(Session::has('success'))
toastr.success('{{ Session::get('success') }}')
@endif

        // metalwt
        $('#metalwt').on('input', function(e) {
    var metalwt = $(this).val();

    // Remove any extra dots after the first one
    var sanitizedValue = metalwt.replace(/\.+/g, '.');

    // Remove non-numeric characters except for the first dot
    sanitizedValue = sanitizedValue.replace(/[^0-9]/g, '');

    // Allow any number of digits before and after the point
    var match = sanitizedValue.match(/^(\d*\.?\d{0,3})$/);
    if (match) {
        sanitizedValue = match[0];
    } else {
        // If the input doesn't match the pattern, remove all dots
        sanitizedValue = sanitizedValue.replace(/\./g, '');
    }

    // Update the input field with the sanitized value
    $(this).val(sanitizedValue);
});

// Your existing change logic remains unchanged
$('#metalwt').on('change', function() {
    var metalwt = $(this).val();
    var sanitizedValue = metalwt.replace(/[^0-9.]/g, '');

    $(this).val(sanitizedValue);

    if (metalwt.length == 4) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1));
    } else if (metalwt.length > 4) {
        var lastThreeDigits = metalwt.slice(-3);
        var firstDigits = metalwt.slice(0, -3);
        $(this).val(firstDigits + "." + lastThreeDigits);
    } else {
        if (metalwt.indexOf('.') !== -1) {
            var parts = metalwt.split('.');
            parts[1] = parts[1].substring(0, 3);
            metalwt = parts.join('.');
        } else {
            metalwt = metalwt.substring(0, 4);
        }

        $(this).val(metalwt);
    }
});

// Your existing change logic remains unchanged
$('#metalwt').change('input', function(e) {
    var metalwt = $(this).val();

    if (metalwt.length == 1) {
        $(this).val(metalwt + ".000");
    } else if (metalwt.length == 2) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1) + "00");
    } else if (metalwt.length == 3) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1) + "0");
    }
});


        // HandleBy

        $(document).ready(function() {
        $('#handleby').autocomplete({
            source: '{{ route('fetchdata') }}',
            minLength: 1 // Minimum number of characters before making an AJAX request
        });
    });

    $(document).ready(function() {
            // Restrict input to numeric values only
            $('#charges').on('input', function() {
                var inputValue = $(this).val();
                var sanitizedValue = inputValue.replace(/[^0-9]/g, ''); // Remove non-numeric characters

                $(this).val(sanitizedValue);
            });
        });

        //Advance

        $(document).ready(function() {
            // Restrict input to numeric values only
            $('#advance').on('input', function() {
                var inputValue = $(this).val();
                var sanitizedValue = inputValue.replace(/[^0-9]/g, ''); // Remove non-numeric characters

                $(this).val(sanitizedValue);
            });
        });

        function validateFileType() {
            // Get the file input element
            var fileInput = document.getElementById('orderimage');

            // Get the selected file
            var file = fileInput.files[0];

            // Check if a file is selected
            if (file) {
                // Get the file extension
                var fileExtension = file.name.split('.').pop().toLowerCase();

                // Check if the file extension is jpg or png
                if (fileExtension !== 'jpg' && fileExtension !== 'jpeg' && fileExtension !== 'png' && fileExtension !== 'gif') {
                    // Show an error message
                    alert('Please select a JPG or PNG or GIF file.');
                    // Clear the file input
                    fileInput.value = '';
                }
            }
        }


    </script>

@endsection


{{--
// metal-weight

  $('#metalwt').on('input', function() {
            var metalwt = $(this).val();

            // Remove non-numeric characters except for the first dot
            var sanitizedValue = metalwt.replace(/[^0-9.]/g, '');

            // Allow any number of digits before the point and limit to 3 digits after the point
            var match = sanitizedValue.match(/^(\d*\.?\d{0,3})$/);
            if (match) {
                sanitizedValue = match[1];
            } else {
                // If the input doesn't match the pattern, remove the last character
                sanitizedValue = sanitizedValue.slice(0, -1);
            }

            // Update the input field with the sanitized value
            $(this).val(sanitizedValue);
        });

        $('#metalwt').on('change', function() {
            var metalwt = $(this).val();

            // Ensure there is at most one point
            var pointCount = (metalwt.match(/\./g) || []).length;
            if (pointCount > 1) {
                // If more than one point is entered, remove the last one
                $(this).val(metalwt.slice(0, -1));
            }

            // Format the input to always have three digits after the point
            var parts = metalwt.split('.');
            if (parts[1]) {
                parts[1] = parts[1].padEnd(3, '0').substring(0, 3);
            } else {
                parts[1] = '000';
            }

            $(this).val(parts.join('.'));
        });


 --}}


 {{--
    // handleby

    $(document).ready(function() {
    var query = $(this).val();
    $('#handleby').select2({
        tags: true,
        createTag: function(params) {
            var term = $.trim(params.term);

            if (term === '') {
                return null;
            }

            return {
                id: term,
                text: term,
                newTag: true // Add a flag to indicate that this is a new tag
            };
        },
        ajax: {
            url: '{{ route('fetchdata') }}',
            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                id: query
            },
            dataType: 'json',
            delay: 250,
            processResults: function(data, params) {

                var uniqueNames = Array.from(new Set(data.map(item => item.handleby)));
                return {
                    results: $.map(uniqueNames, function(name) {
                        return {
                            id: name, // Use 'handleby' as the unique identifier
                            text: name
                        }
                    })
                };
            },
            cache: true
        }
    });
}); --}}

{{--
//metal-weight

$('#metalwt').on('input', function() {
    var metalwt = $(this).val();

    var pointCount = (metalwt.match(/\./g) || []).length;
    if (pointCount > 1) {
        // If more than one point is entered, remove the last one
        $(this).val(metalwt.slice(0, -1));
    }

    // Remove non-numeric characters except for the first dot
    var sanitizedValue = metalwt.replace(/[^0-9]/g, '');

    // Allow any number of digits before the point and limit to 3 digits after the point
    var match = sanitizedValue.match(/^(\d*\.?\d{0,3})$/);
    if (match) {
        sanitizedValue = match[1];
    } else {
        // If the input doesn't match the pattern, remove the last character
        sanitizedValue = sanitizedValue.slice(0, -1);
    }

    // Update the input field with the sanitized value
    $(this).val(sanitizedValue);
});


$('#metalwt').on('change', function() {
    var metalwt = $(this).val();
    var sanitizedValue = metalwt.replace(/[^0-9]/g, ''); // Remove non-numeric characters

    $(this).val(sanitizedValue);

    if (metalwt.length == 4) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1));
    } else if (metalwt.length > 4) {
        var lastThreeDigits = metalwt.slice(-3); // Get the last 3 digits
        var firstDigits = metalwt.slice(0, -3); // Get all digits except the last 3
        $(this).val(firstDigits + "." + lastThreeDigits);
    } else {
        // Limit to 7 characters (including ".")
        if (metalwt.indexOf('.') !== -1) {
            var parts = metalwt.split('.');
            parts[1] = parts[1].substring(0, 3); // Limit to 3 digits after the decimal point
            metalwt = parts.join('.');
        } else {
            metalwt = metalwt.substring(0, 4); // Limit to 3 digits before the decimal point
        }

        $(this).val(metalwt);
    }



});


$('#metalwt').change('input', function(e) {
    var metalwt = $(this).val();

    // If no digits are entered, add "0.000"
    if (metalwt.length == 1) {
        $(this).val(metalwt + ".000");
    } else if (metalwt.length == 2) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1) + "00");
    } else if (metalwt.length == 3) {
        $(this).val(metalwt.substring(0, 1) + "." + metalwt.substring(1) + "0");
    }
});



--}}
