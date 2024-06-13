<style>
    .glyphicon { margin-right:5px;}
    /*.rating .glyphicon {font-size: 22px;}*/
    .rating-num { margin-top:0px;font-size: 54px; }
    .progress { margin-bottom: 5px;}
    .progress-bar { text-align: left; }
    .rating-desc .col-md-3 {padding-right: 0px;}
    .sr-only { margin-left: 5px;overflow: visible;clip: auto; }
    .stars i{

        font-size: 18px;
        color: #28a745;
    }
    .font-weight-bold{
        font-weight: bold;
    }

    ::ng-deep .dropdown-menu {
        min-width: 100% !important;
    }

    ::ng-deep typeahead-container {
        width: 100% !important;
    }
</style>
<div class="product-info-tab bg-white rounded-2 overflow-hidden pt-6 mt-4">
    <ul class="nav nav-tabs border-bottom justify-content-center gap-5 pt-info-tab-nav">
        <li><a href="#description" class="active" data-bs-toggle="tab">{{ localize('Description') }}</a></li>
{{--        <li><a href="#info" data-bs-toggle="tab">{{ localize('Additional Information') }}</a></li>--}}

    </ul>
    <div class="tab-content">
        <div class="tab-pane fade show active px-4 py-5" id="description">
            @if ($product->description)
                {!! $product->description !!}
            @else
                <div class="text-dark text-center border py-2">{{ localize('Not Available') }}
                </div>
            @endif
        </div>
        <div class="tab-pane fade px-4 py-5" id="info">
            <h6 class="mb-2">{{ localize('Additional Information') }}:</h6>
            <table class="w-100 product-info-table">
                @forelse (generateVariationOptions($product->variation_combinations) as $variation)
                    <tr>
                        <td class="text-dark fw-semibold">{{ $variation['name'] }}</td>
                        <td>
                            @foreach ($variation['values'] as $value)
                                {{ $value['name'] }}@if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td class="text-dark text-center" colspan="2">{{ localize('Not Available') }}
                            </td>
                        </tr>
                    @endforelse
                </table>
            </div>

        </div>
    </div>
{{--Rating Star--}}
<div class="product-info-tab bg-white rounded-2 overflow-hidden pt-6 mt-4">
    <div class="tab-content">
{{--        New--}}
        <div class="container">
            <h3 class="text-dark text-center  py-2">Rating & Reviews</h3>
            <div class="row justify-content-center">
                <div class="col-xs-12 col-md-6">
                    <div class="well well-sm">
                        <div class="row">
                            <div class="col-xs-12 col-md-6 text-center">
                                <h1 class="rating-num">
                                    {{ $overAllAverageRating }}</h1>
                                <div class="stars">
                                    @for($i=1;$i<=$overAllAverageRating;$i++)
                                        <i class="fa fa-star"></i>
                                    @endfor
                                </div>
                                <div>
                                    <span class="text-description">{{number_format($overAllAverageRating, 1)}} rating out of 5</span>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-6">
                                <div class="row rating-desc">

                                    @php
                                        $maximumRating = 5; // Adjust this based on your rating scale

                                        // Loop through each rating
                                        for ($rating = $maximumRating; $rating >= 1; $rating--) {
                                            // Assuming you have a variable $individualRatings containing rating data
                                            $ratingData = $individualRating->where('rating', $rating)->first();

                                            // If no data is available for this rating, set default values
                                            $averageRating = $ratingData ? $ratingData->average_rating : 0;
                                            $ratingPercentage = ($averageRating / $maximumRating) * 100;

                                            // Output the dynamic HTML
                                    @endphp

                                    <div class="col-xs-3 col-md-3 text-right">
                                        <span class="glyphicon glyphicon-star"></span>{{ $rating }}
                                    </div>
                                    <div class="col-xs-8 col-md-9">
                                        <div class="progress">
                                            <div class="progress-bar {{ $ratingPercentage >= 60 ? 'progress-bar-success' : ($ratingPercentage >= 40 ? 'progress-bar-info' : ($ratingPercentage >= 20 ? 'progress-bar-warning' : 'progress-bar-danger')) }}"
                                                 role="progressbar" aria-valuenow="{{ $ratingPercentage }}"
                                                 aria-valuemin="0" aria-valuemax="100" style="width: {{ $ratingPercentage }}%">
                                                {{-- <span class="sr-only">{{ $ratingPercentage }}%</span> --}}
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end {{ $rating }} -->

                                    @php
                                        }
                                    @endphp
                                    <!-- end 1 -->
                                </div>
                                <!-- end row -->
                            </div>
                        </div>
{{--                        Comment--}}
                        <div class="row">

                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--        End New--}}
        <div class="tab-pane fade show active px-4 py-0">
{{--                <div class="text-dark text-center  py-2">--}}

{{--                </div>--}}
            <div class=" text-dark text-center border-top py-2">
                <h3>Product Reviews</h3>
            </div>
{{--            Review--}}
            <div class="d-flex row" id="show-product-reviews">

            </div>
        </div>
    </div>
</div>
<!-- Modal -->
{{--<div class="modal fade" id="ImageModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}
{{--  --}}{{--slider--}}
{{--                <div class="container">--}}
{{--                <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">--}}
{{--                    <div class="carousel-inner">--}}
{{--                        <div class="carousel-item active">--}}
{{--                            <img src="{{ staticAsset('images/avatar.jpg') }}" class="d-block w-100" alt="Product_Image">--}}
{{--                        </div>--}}
{{--                        <div class="carousel-item">--}}
{{--                            <img src="{{ staticAsset('images/avatar-one.jpg') }}" class="d-block w-100" alt="Product_Image">--}}
{{--                        </div>--}}
{{--                        <div class="carousel-item">--}}
{{--                            <img src="{{ staticAsset('images/avatar.jpg') }}" class="d-block w-100" alt="Product_Image">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">--}}
{{--                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>--}}
{{--                        <span class="visually-hidden">Previous</span>--}}
{{--                    </button>--}}
{{--                    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">--}}
{{--                        <span class="carousel-control-next-icon" aria-hidden="true"></span>--}}
{{--                         <span class="visually-hidden">Next</span>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--                </div>--}}
{{--  --}}{{-- End slider--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--End model--}}
</div>