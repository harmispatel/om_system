@extends('admin.layouts.admin-layout')

@section('title', 'qrscanner')

@section('content')

{{-- Page Title --}}
<div class="pagetitle">
    <h1>QrScanner</h1>
    <div class="row">
        <div class="col-md-8">
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Qr-Scanner</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section dashboard">
    <div class="row">
        {{-- Errors Message --}}
        @if (session()->has('errors'))
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('errors') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        @endif
    </div>

    <div class="row">

        {{-- Order Listing Card --}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
					<div id="you-qr-result"></div>
					<div style="display:flex;justify-content: center;">
						<div id="my-qr-reader" style="width: 600px;">

						</div>
					</div>
					</div>
            </div>
        </div>
    </div>
</section>
@endsection


	{{-- Custom JS --}}
    @section('page-js')
    <!-- load library -->
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        function domReady(fn){
            if(document.readyState == "complete" || document.readyState == "interactive"){
                setTimeout(fn,1)
            }else{
                document.addEventListener("DOMContentLoaded",fn)
            }
        }
        domReady(function(){
            var myqr = document.getElementById('you-qr-result')
            var lastResult,countResults = 0;

            function onScanSuccess(decodeText,decodeResult){
                if(decodeText !== lastResult){
                    ++countResults;
                    lastResult=decodeText;

                    alert('you Qr is :' + decodeText,decodeResult)
					window.location.href = decodeText;
                    myqr.innerHTML = `you scan ${countResults} : ${decodeText}`


                }
            }
            var htmlscanner = new Html5QrcodeScanner(
                "my-qr-reader",{fps:10,qrbox:250})
                htmlscanner.render(onScanSuccess)
        })
    </script>
	@endsection
