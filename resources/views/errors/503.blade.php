@extends('frontend.default.layouts.master')
@section('title')
    Under Maintenance
@endsection
@section('contents')
    <section class="section-404 ptb-120 position-relative overflow-hidden z-1">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/frame-circle.svg') }}" alt="frame circle"
            class="position-absolute z--1 frame-circle d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/cauliflower.png') }}" alt="cauliflower"
            class="position-absolute cauliflower z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/leaf.svg') }}" alt="leaf"
            class="position-absolute leaf z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/pata-xs.svg') }}" alt="pata"
            class="position-absolute pata z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/tomato-half.svg') }}" alt="tomato"
            class="position-absolute tomato-half z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/garlic-white.png') }}" alt="garlic"
            class="position-absolute garlic-white z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/tomato-slice.svg') }}" alt="tomato"
            class="position-absolute tomato-slice z--1 d-none d-sm-block">
        <img src="{{ staticAsset('frontend/default/assets/img/shapes/onion.png') }} " alt="onion"
            class="position-absolute onion z--1 d-none d-sm-block">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6">
                    <div class="content-404 text-center">
                        <img src="{{ staticAsset('frontend/default/assets/img/503.svg') }}" alt="not found"
                            class="img-fluid">

                           <h6 class="pt-1">Dear Customer,</h6>
                            <p class="pb-0 mb-0">We are currently upgrading our website and will be back online soon.</p>
                            <p class="pt-0 mt-0 ">
                            Meanwhile, you can order by connecting on our regional whatsapp numbers,
                            </p>

                            <h6>Lahore – 0321-3725337</h6>
                            <h6>Islamabad – 0318-5376228</h6>
                            <h6>Rawalpindi – 0318-5376227</h6>

                            <h5>Thank you.</h5>

                            Team Esajee's
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
