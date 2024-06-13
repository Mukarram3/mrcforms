<!DOCTYPE html>
@php
    $locale = str_replace('_', '-', app()->getLocale()) ?? 'en';
    $localLang = \App\Models\Language::where('code', $locale)->first();
@endphp
@if ($localLang->is_rtl == 1)
    <html dir="rtl" lang="{{ $locale }}" data-bs-theme="light">
@else
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="light">
@endif

<head>
    <!--required meta tags-->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!--meta-->
    <meta name="robots" content="index, follow">
    <meta name="description" content="{{ getSetting('global_meta_description') }}">
    <meta name="keywords" content="{{ getSetting('global_meta_keywords') }}">

    <!--favicon icon-->
    <link rel="icon" href="{{ uploadedAsset(getSetting('favicon')) }}" type="image/png" sizes="24x24">

{{--    WHATSAPP ICON --}}
{{--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">--}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">--}}


    <!--title-->
    <title>
        @yield('title', getSetting('system_title'))
    </title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0QYD39J7FJ"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-0QYD39J7FJ');
    </script>

    @yield('meta')



    @if (!isset($detailedProduct) && !isset($blog))
        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="{{ getSetting('global_meta_title') }}" />
        <meta itemprop="description" content="{{ getSetting('global_meta_description') }}" />
        <meta itemprop="image" content="{{ uploadedAsset(getSetting('global_meta_image')) }}" />

        <!-- Twitter Card data -->
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="@publisher_handle" />
        <meta name="twitter:title" content="{{ getSetting('global_meta_title') }}" />
        <meta name="twitter:description" content="{{ getSetting('global_meta_description') }}" />
        <meta name="twitter:creator"
            content="@author_handle"/>
    <meta name="twitter:image" content="{{ uploadedAsset(getSetting('global_meta_image')) }}"/>

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ getSetting('global_meta_title') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('home') }}" />
    <meta property="og:image" content="{{ uploadedAsset(getSetting('global_meta_image')) }}" />
    <meta property="og:description" content="{{ getSetting('global_meta_description') }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" /> 
    <meta property="fb:app_id" content="{{ env('FACEBOOK_PIXEL_ID') }}">
{{--       OWL CSS--}}
        @yield('css')
{{--        End Owl--}}

{{--        --}}{{--CITES MODEL CSS--}}
        @yield('cites-css')
        {{--CITES MODEL CSS--}}
{{--        Search--}}
        @yield('search-option')
@endif

    <!-- head-scripts -->
    @include('frontend.default.inc.head-scripts')
    <!-- head-scripts -->

    <!--build:css-->
    @include('frontend.default.inc.css', ['localLang' => $localLang])

    <!-- endbuild -->
{{--google map--}}
{{--    <script--}}
{{--            src="https://maps.googleapis.com/maps/api/js?key=Your Api?=" defer--}}
{{--    ></script>--}}


{{--    <script>--}}
{{--        function initMap() {--}}
{{--            const myLatlng = { lat: -37.323, lng: 122.0322 };--}}
{{--            const map = new google.maps.Map(document.getElementById("map"), {--}}
{{--                zoom: 4,--}}
{{--                center: myLatlng,--}}
{{--            });--}}
{{--            // Create the initial InfoWindow.--}}
{{--            let infoWindow = new google.maps.InfoWindow({--}}
{{--                content: "Click the map to get Lat/Lng!",--}}
{{--                position: myLatlng,--}}
{{--            });--}}

{{--            infoWindow.open(map);--}}
{{--            // Configure the click listener.--}}
{{--            map.addListener("click", (mapsMouseEvent) => {--}}
{{--                // Close the current InfoWindow.--}}
{{--                infoWindow.close();--}}
{{--                // Create a new InfoWindow.--}}
{{--                infoWindow = new google.maps.InfoWindow({--}}
{{--                    position: mapsMouseEvent.latLng,--}}
{{--                });--}}
{{--                infoWindow.setContent(--}}
{{--                    JSON.stringify(mapsMouseEvent.latLng.toJSON(), null, 2),--}}
{{--                );--}}
{{--                infoWindow.open(map);--}}
{{--            });--}}
{{--        }--}}

{{--        window.initMap = initMap;--}}

{{--    </script>--}}

{{--    Second--}}
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAJfzOqR9u2eyXv6OaiuExD3jzoBGGIVKY&libraries=geometry,places&v=weekly"></script>

<script>
    var overlay;

    testOverlay.prototype = new google.maps.OverlayView();

    function initialize() {
        var map = new google.maps.Map(document.getElementById("map"), {
            zoom: 15,
            center: {
                lat: 37.323,
                lng: -122.0322
            },
            mapTypeId: "terrain",
            draggableCursor: "crosshair"
        });
        map.addListener("click", (event) => {
            map.setCenter(event.latLng);
            console.log(event.latLng.toString());
        });

        overlay = new testOverlay(map);

        var input =
            /** @type {HTMLInputElement} */
            (document.getElementById("pac-input"));
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
            /** @type {HTMLInputElement} */ (input)
        );

        google.maps.event.addListener(searchBox, "places_changed", function () {
            var places = searchBox.getPlaces();
            if (places.length == 0) {
                return;
            }
            map.setCenter(places[0].geometry.location);
        });
    }

    function testOverlay(map) {
        this.map_ = map;
        this.div_ = null;
        this.setMap(map);
    }

    testOverlay.prototype.onAdd = function () {
        var div = document.createElement("div");
        this.div_ = div;
        div.style.borderStyle = "none";
        div.style.borderWidth = "0px";
        div.style.position = "absolute";
        div.style.left = -window.innerWidth / 2 + "px";
        div.style.top = -window.innerHeight / 2 + "px";
        div.width = window.innerWidth;
        div.height = window.innerHeight;

        const canvas = document.createElement("canvas");
        canvas.style.position = "absolute";
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        div.appendChild(canvas);

        const panes = this.getPanes();
        panes.overlayLayer.appendChild(div);

        var ctx = canvas.getContext("2d");
        this.drawLine(ctx, 0, "rgba(0, 0, 0, 0.2)");
        this.drawLine(ctx, 90, "rgba(0, 0, 0, 0.2)");
        this.drawLine(ctx, 37.5, "rgba(255, 0, 0, 0.4)");
        this.drawLine(ctx, 67.5, "rgba(255, 0, 0, 0.4)");
    };

    testOverlay.prototype.drawLine = function (ctx, degrees, style) {
        // 0 north, growing clockwise
        const w = window.innerWidth / 2;
        const h = window.innerHeight / 2;
        const radians = ((90 - degrees) * Math.PI) / 180;
        const hlen = Math.min(w, h);
        const x = Math.cos(radians) * hlen;
        const y = -Math.sin(radians) * hlen;
        ctx.beginPath();
        ctx.strokeStyle = style;
        ctx.moveTo(w - x, h - y);
        ctx.lineTo(w + x, h + y);
        ctx.stroke();
    };

    testOverlay.prototype.onRemove = function () {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    };

    google.maps.event.addDomListener(window, "load", initialize);

</script>
</head>

<body>

    @php
        // for visitors to add to cart
        $tempValue = strtotime('now') . rand(10, 1000);
        $theTime = time() + 86400 * 365;
        if (!isset($_COOKIE['guest_user_id'])) {
            setcookie('guest_user_id', $tempValue, $theTime, '/'); // 86400 = 1 day
        }
        
    @endphp

    <!--preloader start-->
{{--    <div id="preloader">--}}
{{--        <img src="{{ staticAsset('frontend/default/assets/img/preloader.gif') }}" alt="preloader" class="img-fluid">--}}
{{--    </div>--}}
    <!--preloader end-->

    <!--main content wrapper start-->
    <div class="main-wrapper">
        <!--header section start-->
        @if (isset($exception))
            @if ($exception->getStatusCode() != 503)
                @include('frontend.default.inc.header')
            @endif
        @else
            @include('frontend.default.inc.header')
        @endif
        <!--header section end-->

        <!--breadcrumb section start-->
        @yield('breadcrumb')
        <!--breadcrumb section end-->

        <!--offcanvas menu start-->
        @include('frontend.default.inc.offcanvas')
        <!--offcanvas menu end-->

        @yield('contents')

        <!-- modals -->
        @include('frontend.default.pages.partials.products.quickViewModal')
        <!-- modals -->


{{--Whatsapp--}}
        <?php
        $phone = "";

        if (\Illuminate\Support\Facades\Session::has('city')) {
            $city_id = \Illuminate\Support\Facades\Session::get('city');

            $phone = \App\Models\Branch::whereHas('cities', function ($q) use ($city_id) {
                $q->where('city_id', $city_id);
            })->latest()->value('phone');
        }
        ?>



{{--            <a href="https://whatsapp.com/send?phone={{ $phone }}&text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202." class="whatsup_link" target="_blank">--}}
{{--                --}}{{--            <i class="fa fa-whatsapp whats_up"></i>--}}
{{--                <i class="fa-brands fa-whatsapp whats_up spin-animation"></i>--}}
{{--            </a>--}}

        <a href="whatsapp://send?phone={{ $phone }}&text=Hello" class="whatsup_link" target="_blank">
            {{--            <i class="fa fa-whatsapp whats_up"></i>--}}
            <i class="fa-brands fa-whatsapp whats_up spin-animation"></i>
        </a>

{{--        End Whatsapp--}}



        <!--footer section start-->
        @if (isset($exception))
            @if ($exception->getStatusCode() != 503)
                @include('frontend.default.inc.footer')
                @include('frontend.default.inc.bottomToolbar')
            @endif
        @else
            @include('frontend.default.inc.footer')
            @include('frontend.default.inc.bottomToolbar')
        @endif
        <!--footer section end-->

    </div>


    <div class="modal fade" id="mentenanceMode"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 536px;">
            <div class="modal-content">
                <div class="modal-body">
                    <img
                            src="{{ uploadedAsset(getSetting('navbar_logo')) }}" alt="logo" class="img-fluid">
                </div>
                <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div> -->
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="locationmodel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 536px;">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="{{ route('change.city') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center">
                            <img src="{{ uploadedAsset(getSetting('navbar_logo')) }}" alt="logo" class="img-fluid">
                            <h3>Please Select Your City</h3>
                            <!-- <a class="btn btn-warning btn-rounded" href="#" role="button">Deliver</a> -->
                        </div>


                        <div class="form-group">
                            <select id="selectElement" name="city_id" class="js-example-basic-single">
                                <option value="">Select Cities</option>
                                @foreach(\App\Models\City::where('is_active',1)->get() as $city)
                                    <option value="{{$city->id}}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-grid gap-2 pt-5">
                            <button  class="btn btn-warning" type="submit">Select</button>
                        </div>
                    </form>
                </div>
                <!-- <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-primary">Save changes</button>
                </div> -->
            </div>
        </div>
    </div>


    <!--scroll bottom to top button start-->
    <button class="scroll-top-btn">
        <i class="fa-regular fa-hand-pointer"></i>
        </button>
        <!--scroll bottom to top button end-->

       <!--build:js-->
        @include('frontend.default.inc.scripts')
        <!--endbuild-->


    <!--page's scripts-->
        @yield('scripts')
        <!--page's script-->
{{--owl script--}}
    @yield('script')


{{--Owl Script--}}
    <script>
        $( document ).ready(function() {
            OpenBootstrapPopup();
            // $('#mentenanceMode').modal('show');
        });
        // window.onload = function () {
        //     OpenBootstrapPopup();
        //     // allProduct();
        //     $('#mentenanceMode').modal('show');
        // };
        function OpenBootstrapPopup() {
            @if(\Illuminate\Support\Facades\Session::has('city'))
            $("#locationmodel").modal('hide');
{{--            @elseif(env('DEMO_MODE') == 'Off')--}}
{{--            $("#locationmodel").modal('hide');--}}
            @else
            $("#locationmodel").modal('show');
            @endif
        }
        function showModal(){
            $("#locationmodel").modal('show');
        }

        // SELECT

        $(document).ready(function() {
            $('#selectElement').select2({
                width: '100%', // Adjust the width as needed
                searchInputPlaceholder: 'Search City',
                placeholder: 'Search for an option', // Placeholder text
                allowClear: true, // Option to clear the selection
                dropdownParent:$('#locationmodel')
            });

            $('#selectElement').one('select2:open', function(e) {
                $('input.select2-search__field').prop('placeholder', 'Search City');
            });
        });

        {{--var totalproduct=12;--}}

        {{--function allProduct(){--}}
        {{--    $("#skelton").show();--}}
        {{--    $.ajax({--}}
        {{--        type: 'GET',--}}
        {{--        url: "{{ url('/all-products') }}",--}}
        {{--        success: function (results) {--}}
        {{--            console.log(results);--}}
        {{--            $("#skelton").hide();--}}
        {{--            $("#allProducts").append(results.html);--}}
        {{--            totalproduct = 24;--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}

        {{--var Hostrl = "{{ url('/') }}";--}}
        {{--$("#loadmore").on('click',function (){--}}
        {{--    $("#spinner").show();--}}
        {{--    console.log("helo world "+totalproduct);--}}
        {{--    $.ajax({--}}
        {{--        type: 'GET',--}}
        {{--        url: Hostrl+"/all-products?total="+totalproduct,--}}
        {{--        success: function (results) {--}}
        {{--            totalproduct = results.totalcount;--}}
        {{--            console.log(results.totalcount);--}}
        {{--            $("#allProducts").append(results.html);--}}
        {{--            $("#spinner").hide();--}}
        {{--        }--}}
        {{--    });--}}
        {{--})--}}
    </script>

    <script>
        $(document).on('click', function(event) {
            // Check if the clicked element is not the search button or its child
            if (!$(event.target).closest('.search-btn').length) {
                $('.dropdown-search').css('display', 'none');
            }
        });

        $('.search-btn').on('click', function(event) {
            event.stopPropagation();

            $('.dropdown-search').css({
                "position": "absolute",
                "inset": "0px 0px auto auto",
                "margin": "0px",
                "transform": "translate3d(0px, 26.6667px, 0px)",
                "display": "block"
            });
        });



    </script>

{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"> </script>
    <script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>

    @yield('javascript')


    <script type="text/javascript">
        var route = "{{ url('autocomplete-search') }}";

        $('#autocomplete').typeahead({
            source: function (query, process) {
                return $.get(route, {
                    query: query,
                }, function (data) {
                    console.log(data);
                    return process(data);
                });
            },
            updater: function (item) {
                // Check if the Enter key was pressed
                if (event.keyCode === 13) {
                    // Optionally, you can perform some custom action here
                    console.log("Enter key pressed, but not selecting the item");
                    $('#searchform').submit();
                    return this.$element.val(); // Return the current input value
                } else {
                    // Continue with the default behavior for other keys
                    return item;
                }
            },
            highlighter: function (item) {
                // Customize the way items are highlighted in the dropdown
                // You can customize this based on your needs
                return item;
            },
        }).on('change', function () {
            // Handle the change event here
            var selectedItem = $(this).typeahead("getActive");
            console.log("Item selected:", selectedItem ? selectedItem.name : null);

            // Optionally, you can perform additional actions
            // Note: The next line is unreachable because the previous line has a return statement
            // return this.$element.val(selectedItem ? selectedItem.name : null);

            $('#searchform').submit();
        });
        {{--var route = "{{ url('autocomplete-search') }}";--}}
        {{--$('#bottom-search').typeahead({--}}
        {{--    source: function (query, process) {--}}
        {{--        return $.get(route, {--}}
        {{--            query: query,--}}
        {{--        }, function (data) {--}}
        {{--            console.log(data);--}}
        {{--            return process(data);--}}
        {{--        });--}}
        {{--    },--}}
        {{--    updater: function (item) {--}}
        {{--        // Check if the Enter key was pressed--}}
        {{--        if (event.keyCode === 13) {--}}
        {{--            // Optionally, you can perform some custom action here--}}
        {{--            console.log("Enter key pressed, but not selecting the item");--}}


        {{--            $('#searchform').submit();--}}
        {{--            return this.$element.val(); // Return the current input value--}}

        {{--        } else {--}}
        {{--            // Continue with the default behavior for other keys--}}
        {{--            return item;--}}
        {{--            // $('#searchform').submit();--}}

        {{--        }--}}
        {{--    },--}}
        {{--    highlighter: function (item) {--}}
        {{--        // Customize the way items are highlighted in the dropdown--}}
        {{--        // You can customize this based on your needs--}}
        {{--        return item;--}}
        {{--    },--}}
        {{--}).on('change', function () {--}}
        {{--    var selectedItem = $(this).typeahead("getActive");--}}
        {{--    console.log("Item selected:", selectedItem ? selectedItem.name : null);--}}

        {{--    // Optionally, you can perform additional actions--}}
        {{--    // Note: The next line is unreachable because the previous line has a return statement--}}
        {{--    // return this.$element.val(selectedItem ? selectedItem.name : null)--}}


        {{--    $('#autocomplete').val(selectedItem.name)--}}
        {{--    $('#searchform').submit();--}}
        {{--});--}}
// ======

        var route = "{{ url('autocomplete-search') }}";

        $('#bottom-search').typeahead({
            source: function (query, process) {
                return $.get(route, {
                    query: query,
                }, function (data) {
                    console.log(data);
                    return process(data);
                });
            },
            updater: function (item) {
                // Check if the Enter key was pressed
                if (event.keyCode === 13) {
                    // Optionally, you can perform some custom action here
                    console.log("Enter key pressed, but not selecting the item");
                    $('#bottomserahcform').submit();
                    return this.$element.val(); // Return the current input value
                } else {
                    // Continue with the default behavior for other keys
                    return item;
                }
            },
            highlighter: function (item) {
                // Customize the way items are highlighted in the dropdown
                // You can customize this based on your needs
                return item;
            },
        }).on('change', function () {
            // Handle the change event here
            var selectedItem = $(this).typeahead("getActive");
            console.log("Item selected:", selectedItem ? selectedItem.name : null);

            // Optionally, you can perform additional actions
            // Note: The next line is unreachable because the previous line has a return statement
            // return this.$element.val(selectedItem ? selectedItem.name : null);

            $('#bottomserahcform').submit();
        });
    </script>

@yield('cite-script')

        </body>

        </html>
