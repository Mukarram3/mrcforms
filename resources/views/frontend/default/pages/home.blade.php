@extends('frontend.default.layouts.master')

@section('title')
    {{ localize('Home') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <!--hero section start-->
    @include('frontend.default.pages.partials.home.hero')
    <!--hero section end-->

    <!--category section start-->
{{--    @include('frontend.default.pages.partials.home.category')--}}
    <!--category section end-->

    <!--featured products start-->
{{--    @include('frontend.default.pages.partials.home.featuredProducts')--}}
    <!--featured products end-->

    <!--trending products start-->
    @include('frontend.default.pages.partials.home.trendingProducts')
    <!--trending products end-->

    <!--banner section start-->
    @include('frontend.default.pages.partials.home.banners')
    <!--banner section end-->

    <!--banner section start-->
{{--    @include('frontend.default.pages.partials.home.bestDeals')--}}
    <!--banner section end-->

    <!--banner 2 section start-->
{{--    @include('frontend.default.pages.partials.home.bannersTwo')--}}
    <!--banner 2 section end-->

    <!--feedback section start-->
    @include('frontend.default.pages.partials.home.feedback')
    <!--feedback section end-->

    <!--products listing start-->
{{--    @include('frontend.default.pages.partials.home.products')--}}
    <!--products listing end-->

    <!--blog section start-->
    @include('frontend.default.pages.partials.home.blogs', ['blogs' => $blogs])
    <!--blog section end-->



@endsection

@section('javascript')

    <script>
        window.onload = function () {
            if (typeof allProduct === 'function') {
                allProduct();
            } else {
                console.error('allProduct function is not defined.');
            }
        };


        var totalproduct=12;

        function allProduct(){
            $("#skelton").show();
            $.ajax({
                type: 'GET',
                url: "{{ url('/all-products') }}",
                success: function (results) {
                    console.log(results);
                    $("#skelton").hide();
                    $("#allProducts").append(results.html);
                    totalproduct = 24;
                }
            });
        }
        var Hostrl = "{{ url('/') }}";
        $("#loadmore").on('click',function (){
            $("#spinner").show();
            console.log("helo world "+totalproduct);
            $.ajax({
                type: 'GET',
                url: Hostrl+"/all-products?total="+totalproduct,
                success: function (results) {
                    totalproduct = results.totalcount;
                    console.log(results.totalcount);
                    $("#allProducts").append(results.html);
                    $("#spinner").hide();
                }
            });
        })
    </script>
    <script>
        "use strict";

        // runs when the document is ready 
        {{--$(document).ready(function() {--}}
        {{--    @if (\App\Models\Location::where('is_published', 1)->count() > 1)--}}
        {{--        notifyMe('info', '{{ localize('Select your location if not selected') }}');--}}
        {{--    @endif--}}
        {{--});--}}


    </script>
@endsection
