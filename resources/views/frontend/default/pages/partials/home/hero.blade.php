@php
    $homefirstsliders = \Illuminate\Support\Facades\DB::table('home_owl_sliders')->where('type','first_slider')->get();
    $homeSecondSlider = \Illuminate\Support\Facades\DB::table('home_owl_sliders')->where('type','Second_Slider')->get();
    $homeThirdSlider = \Illuminate\Support\Facades\DB::table('home_owl_sliders')->where('type','third_slider')->get();

     //$categories = Category::latest()->get();
//    $categories = \Illuminate\Support\Facades\DB::table('categories')->where('level',0)->get();
    $categories = \App\Models\Category::with('childrenCategories')->where('level',0)->get();

@endphp

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>

        /*.owl-card{*/
        /*    max-width: 160px;*/
        /*}*/

        #img_responsive {
            width: 100%;

            /*object-fit: cover;*/
            object-position: center;
        }
        .owl-link{
            text-decoration: none;
        }

        .owl-stage-outer{
            border-radius: 5px;
        }

        /*.contain-main{*/
        /*    margin-top: -33px;*/
        /*}*/

        /*#owl-images{*/
        /*    height: 100px;*/
        /*    width: 100px;*/
        /*}*/


        /*Category*/




        /*END CATEGORY*/
        @media screen and (min-width: 992px){
            #img_responsive {
                width: 100%;
                height: 300px;
                /*object-fit: cover;*/
                object-position: center;
            }

            /*#image_res{*/
            /*    width: 100%;*/
            /*    max-height: 262px;*/
            /*    object-fit: cover;*/
            /*    object-position: center;*/
            /*}*/
        }








    </style>
@endsection
@section('cites-css')
{{--    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">--}}

    <!-- Add Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
{{--    MODEL CITES BLUR--}}
    <style>
        body.modal-open .container{
            -webkit-filter: blur(4px);
            -moz-filter: blur(4px);
            -o-filter: blur(4px);
            -ms-filter: blur(4px);
            filter: blur(4px);
            filter: url("https://gist.githubusercontent.com/amitabhaghosh197/b7865b409e835b5a43b5/raw/1a255b551091924971e7dee8935fd38a7fdf7311/blur".svg#blur);
            filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='4');
        }
    </style>
    {{--    MODEL CITES BLUR--}}
@endsection
<section class="gshop-hero pt-120 bg-white position-relative z-1 overflow-hidden">
{{--start of owl--}}
    <div class="container contain-main">
        <div class="row">
{{--            Category--}}
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 pt-2">
                <ul class="main_bar">
                    @foreach($categories as $category)
                        <li class="maindropdownone list_item_one">
                            <div class="avatar avatar sm" style="width: 1.75rem;height: 1.75rem;line-height: 1.75rem;display: inline-block; margin-right: 10px;">
                                <img class="rounded-circle" style="width: 100%;height: 100%;object-fit: contain;"
                                     src="{{ uploadedAsset($category->collectLocalization('thumbnail_image')) }}"
                                     alt="" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'" />
                            </div>
                            <a href="{{ route('products.index') }}?&category_id={{ $category->id }}">{{ $category->name }}
                                <i class="fa fa-caret-right fa-thin custome_font"></i>
                            </a>
                            @if($category->childrenCategories->isNotEmpty())
                            <ul class="submenu">
                                    @foreach($category->childrenCategories as $child)
                                        <li class="subdropdownone list_item_two">
                                            <a>{{ $child->name }}
                                                <i class="fa fa-caret-right custome_font"></i>

                                            </a>
                                            @if($child->childrenCategories)
                                            <ul class="submenu_drop" >

                                                    @foreach($child->childrenCategories as $sub_child)
                                                        <li class="list_item_two"><a href="{{ route('products.index') }}?&category_id={{ $sub_child->id }}">{{ $sub_child->name }}</a></li>
                                                    @endforeach

                                            </ul>
                                            @endif
                                        </li>
                                    @endforeach
                            </ul>
                            @endif
                        </li>
                    @endforeach

                </ul>
            </div>
{{--            END--}}
            <div class="col-sm-12 col-md-12 col-lg-9 pt-2">
                <div id="one_owl" class="owl-carousel owl-theme">

                    @foreach($homefirstsliders as $firstslider)
                    <div class="item">
{{--                        <a href="#"><img src="{{asset('images/one.jpg')}}" alt="#" class="img-fluid rounded" id="img_responsive"></a>--}}
                        <a href="{{ $firstslider->slider_link }}" target="_blank"><img src="{{ uploadedAsset($firstslider->images) }}" alt="#" class="img-fluid rounded" id="img_responsive"></a>
                    </div>
                    @endforeach
{{--                    <div class="item">--}}
{{--                        <a href="#"><img src="{{asset('images/two.jpg')}}" alt="#" class="img-fluid rounded" id="img_responsive"></a>--}}
{{--                    </div>--}}
{{--                    <div class="item">--}}
{{--                        <a href="#"><img src="{{asset('images/three.jpg')}}" alt="#" class="img-fluid rounded" id="img_responsive"></a>--}}
{{--                    </div>--}}
                </div>
            </div>
            <!-- Sider  Owl -->
            <div class="col-6 col-sm-6 col-md-6 col-lg-3 pt-2 d-none">
                <div id="two_owl" class="owl-carousel owl-theme">
                    @foreach($homeSecondSlider as $secondSlider)
                    <div class="item">
                        <a href="{{ $secondSlider->slider_link }}" target="_blank"><img src="{{ uploadedAsset($secondSlider->images) }}" alt="#" class="img-fluid rounded" id="img_responsive"></a>
                    </div>
                    @endforeach
                </div>
            </div>
            <!-- third owl -->
{{--            <div class="col-6 col-sm-6 col-md-6 col-lg-3 pt-2">--}}
{{--                <div id="three_owl" class="owl-carousel owl-theme">--}}
{{--                    @foreach($homeThirdSlider as $thirdSlider)--}}
{{--                    <div class="item">--}}
{{--                        <a href="{{ $thirdSlider->slider_link  }}" target="_blank"><img src="{{ uploadedAsset($thirdSlider->images) }}" alt="#" class="img-fluid rounded" id="img_responsive"></a>--}}
{{--                    </div>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
            <!-- Products -->

{{--            <div class="col-sm-12 col-md-12 col-lg-12 pt-4 pt-2">--}}
{{--                <div id="product_owl" class="owl-carousel owl-theme">--}}
{{--                    @foreach($categories as $category)--}}
{{--                        @if(file_exists(uploadedAsset($category->thumbnail_image)))--}}
{{--                            <div class="item px-1">--}}
{{--                                <div class="card p-2">--}}

{{--                                    <div class="card-body p-0 m-0">--}}
{{--                                        --}}{{--                                    <p class="card-text text-center">{{ $category->name }}</p>--}}
{{--                                        <a href="{{ url('/products?&category_id='.$category->id) }}" class="text-dark owl-link">--}}

{{--                                            <img src="{{ uploadedAsset($category->thumbnail_image) }}" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'" alt="Category Images" class="img-fluid " style="width: 100%" id="owl-images">--}}

{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                        @endif--}}

{{--                    @endforeach--}}

{{--                    <div class="item">--}}
{{--                        <div class="card p-2" style="width:180px;">--}}
{{--                            <a href="#" class="text-dark owl-link"><img src="{{asset('images/profuct_one.png')}}" alt="#" class="img-fluid mx-auto" id="owl-images">--}}

{{--                                <div class="card-body">--}}
{{--                                    <p class="card-text text-center">Mobile</p>--}}
{{--                                </div></a>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="item">--}}
{{--                        <div class="card p-2" style="width:180px;">--}}
{{--                            <a href="#" class="text-dark owl-link"><img src="{{asset('images/profuct_one.png')}}" alt="#" class="img-fluid mx-auto" id="owl-images">--}}

{{--                                <div class="card-body">--}}
{{--                                    <p class="card-text text-center">Mobile</p>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
    </div>
{{-- End of owl   --}}

{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/leaf-shadow.png') }}" alt="leaf"--}}
{{--        class="position-absolute leaf-shape z--1 rounded-circle d-none d-lg-inline">--}}
{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/mango.png') }}" alt="mango"--}}
{{--        class="position-absolute mango z--1" data-parallax='{"y": -120}'>--}}

{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/hero-circle-sm.png') }}" alt="circle"--}}
{{--        class="position-absolute hero-circle circle-sm z--1 d-none d-md-inline">--}}

{{--    <div class="container">--}}
{{--        <div class="gshop-hero-slider swiper">--}}
{{--            <div class="swiper-wrapper">--}}

{{--                @foreach ($sliders as $slider)--}}
{{--                    <div class="swiper-slide gshop-hero-single">--}}
{{--                        <div class="row align-items-center justify-content-between">--}}
{{--                            <div class="col-xl-5 col-lg-7">--}}
{{--                                <div class="hero-left-content">--}}
{{--                                    <span--}}
{{--                                        class="gshop-subtitle fs-5 text-secondary mb-2 d-block">{{ $slider->sub_title }}</span>--}}
{{--                                    <h1 class="display-4 mb-3">{{ $slider->title }}</h1>--}}
{{--                                    <p class="mb-5 fs-6">{{ $slider->text }}</p>--}}

{{--                                    <div class="hero-btns d-flex align-items-center gap-3 gap-sm-5 flex-wrap">--}}
{{--                                        <a href="{{ $slider->link }}"--}}
{{--                                            class="btn btn-secondary">{{ localize('Explore Now') }}<span--}}
{{--                                                class="ms-2"><i class="fa-solid fa-arrow-right"></i></span></a>--}}
{{--                                        <a href="{{ route('home.pages.aboutUs') }}"--}}
{{--                                            class="btn btn-primary">{{ localize('About Us') }}<span class="ms-2"><i--}}
{{--                                                    class="fa-solid fa-arrow-right"></i></span></a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-xl-6 col-lg-5">--}}
{{--                                <div class="hero-right text-center position-relative z-1 mt-6 mt-xl-0">--}}

{{--                                    <img src="{{ uploadedAsset($slider->image) }}" alt=""--}}
{{--                                        class="img-fluid position-absolute end-0 top-50 hero-img">--}}

{{--                                    <img src="{{ staticAsset('frontend/default/assets/img/shapes/hero-circle-lg.png') }}"--}}
{{--                                        alt="circle shape" class="img-fluid hero-circle">--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}

{{--    </div>--}}
{{--    <div class="at-header-social d-none d-xl-flex align-items-center position-absolute">--}}
{{--        <span class="title fw-medium">{{ localize('Follow on') }}</span>--}}
{{--        <ul class="social-list ms-3">--}}
{{--            <li>--}}
{{--                <a href="{{ getSetting('facebook_link') }}" target="_blank"><i class="fab fa-facebook-f"></i></a>--}}
{{--            </li>--}}
{{--            <li><a href="{{ getSetting('twitter_link') }}" target="_blank"><i class="fab fa-twitter"></i></a></li>--}}
{{--            <li><a href="{{ getSetting('linkedin_link') }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>--}}
{{--            <li><a href="{{ getSetting('youtube_link') }}" target="_blank"><i class="fab fa-youtube"></i></a></li>--}}
{{--        </ul>--}}
{{--    </div>--}}
    <div class="gshop-hero-slider-pagination theme-slider-control position-absolute top-50 translate-middle-y z-5">
    </div>
{{--    MODEL OF CITES--}}
    <!-- Modal -->

{{--    END OF MODEL--}}
</section>
{{--Model--}}
<div class="modal fade" id="locationmodel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 536px;">
        <div class="modal-content">
            <div class="modal-body">
                <form action="{{ route('change.city') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center"><img src="{{ uploadedAsset(getSetting('navbar_logo')) }}" alt="logo" class="img-fluid">
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





<!-- Modal -->

@section('script')
    <!-- OWL CAROUSEL JAVASCRIPT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $('#one_owl').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            dots:false,
            autoplay:true,
            autoplayTimeout: 2000,
            smartSpeed:1500,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        })


        // Sider

        $('#two_owl').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            dots:false,
            autoplay:true,
            autoplayTimeout: 3000,
            smartSpeed:1500,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        })

        // third-sider

        $('#three_owl').owlCarousel({
            loop:true,
            margin:10,
            nav:false,
            dots:false,
            autoplay:true,
            autoplayTimeout: 5000,
            smartSpeed:1500,
            responsive:{
                0:{
                    items:1
                },
                600:{
                    items:1
                },
                1000:{
                    items:1
                }
            }
        })


        // Product

        // $('#product_owl').owlCarousel({
        //     loop:true,
        //     margin:0,
        //     nav:false,
        //     dots:false,
        //     autoplay:true,
        //     autoplayTimeout: 1500,
        //     smartSpeed:1500,
        //     stagePadding:50,
        //     singleItem: true,
        //
        //     responsive:{
        //         375:{
        //             items:3
        //         },
        //         600:{
        //             items:2
        //         },
        //         1000:{
        //             items:7
        //         },
        //         1200:{
        //             items:7
        //         }
        //     }
        // })


    </script>

@endsection
@section('cite-script')
{{--    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>--}}
{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>--}}
{{--    Add Select2 JS--}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection


