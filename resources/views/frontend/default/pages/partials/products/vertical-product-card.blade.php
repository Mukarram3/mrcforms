<style>
    @media only screen and (max-width:420px){


        #image_product_responsive{
            width: 100% !important;
            height:100px !important;
        }
        .line-height{
            line-height: 2.2
        }
    }


    @media only screen and (max-width:453px){
        .add-to-cart-text{
            font-size: 10px !important;
            padding: 10px 5px !important;
        }
    }

    @media only screen and (max-width:438px){
        .add-to-cart-text{
            font-size: 10px !important;
            padding: 10px 5px !important;
        }
    }


    @media only screen and (min-width:420px){
        #image_product_responsive{
            width: 100% !important;
            height:250px !important;

        }
    }
    @media only screen and (max-width: 400px) {
        .button_responsive{
            font-size: 8px !important;
            padding: 10px 14px !important;]

        }
    }

</style>

<div class="vertical-product-card rounded-2 position-relative swiper-slide {{ isset($bgClass) ? $bgClass : '' }}">
    <div class="d-flex gap-2 flex-column">

        @if (Auth::check() && Auth::user()->user_type == 'customer')
            <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"
                                                                 onclick="addToWishlist({{ $product->id }})"></i></a>
        @elseif(!Auth::check())
            <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"
                                                                 onclick="addToWishlist({{ $product->id }})"></i></a>
        @endif
</div>
    @php
        $discountPercentage = discountPercentage($product);
    @endphp

    @if ($discountPercentage > 0)
        <span class="offer-badge text-white fw-bold fs-xxs bg-danger position-absolute start-0 top-0">
            -{{ discountPercentage($product) }}% <span class="text-uppercase">{{ localize('Off') }}</span>
        </span>
    @endif
    {{--saad--}}
    <a href="{{ route('products.show', $product->slug) }}">
        <div class="thumbnail position-relative text-center p-4">
            {{--        All products images--}}
            <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt="{{ $product->name }}"
                 class="img-fluid" id="image_product_responsive" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'">
{{--WISH BUTTON--}}
            <div class="product-btns position-absolute d-flex gap-2 flex-column">
{{--                @if (Auth::check() && Auth::user()->user_type == 'customer')--}}
{{--                    <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"--}}
{{--                                                                         onclick="addToWishlist({{ $product->id }})"></i></a>--}}
{{--                @elseif(!Auth::check())--}}
{{--                    <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"--}}
{{--                                                                         onclick="addToWishlist({{ $product->id }})"></i></a>--}}
{{--                @endif--}}


                <a href="javascript:void(0);" class="rounded-btn" onclick="showProductDetailsModal({{ $product->id }})"><i
                            class="fa-regular fa-eye"></i></a>
            </div>
            {{--WISH BUTTON--}}
        </div>
    </a>
    <div class="card-content">

        <!--product category start-->
        <div class="mb-2 tt-category tt-line-clamp tt-clamp-1 line-height">
            @if ($product->categories()->count() > 0)
                @foreach ($product->categories as $category)
                    <a href="{{ route('products.index') }}?&category_id={{ $category->id }}"
                       class="d-inline-block text-muted fs-xxs">{{ $category->collectLocalization('name') }}
                        @if (!$loop->last)
                            ,
                        @endif
                    </a>
                @endforeach
            @endif
        </div>
        <!--product category end-->

        <a href="{{ route('products.show', $product->slug) }}"
           class="card-title fw-semibold mb-2 tt-line-clamp tt-clamp-1" style="-webkit-line-clamp: 2;text-overflow: ellipsis;height:49px;">{{ $product->name }}
        </a>

        <h6 class="price">
            @include('frontend.default.pages.partials.products.pricing', [
                'product' => $product,
                'onlyPrice' => true,
            ])
        </h6>


        @isset($showSold)
            <div class="card-progressbar mb-2 mt-3 rounded-pill">
                <span class="card-progress bg-primary" data-progress="{{ sellCountPercentage($product) }}%"
                      style="width: {{ sellCountPercentage($product) }}%;"></span>
            </div>
            <p class="mb-0 fw-semibold">{{ localize('Total Sold') }}: <span
                        class="fw-bold text-secondary">{{ $product->total_sale_count }}/{{ $product->sell_target }}</span>
            </p>
        @endisset
        @php
            $isVariantProduct = false;
    //            $isVariantProduct = $product->variations()->count() > 2 ? 1 : 0;
    //         $stock = 0;
    //
    //         if ($isVariantProduct === 0) {
    //             if (\Illuminate\Support\Facades\Session::has('city')) {
    //                 $cityId = \Illuminate\Support\Facades\Session::get('city');
    //
    //                 $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
    //                     $q->where('city_id', $cityId);
    //                 })->latest()->first();
    //
    //                 if ($branch) {
    //                     $productPrice = \App\Models\ProductPrice::where('branch_id', $branch->id)
    //                         ->where('product_id', $product->id)
    //                         ->value('stock_qty');
    //
    //                     if ($productPrice !== null) {
    //                         $stock = $productPrice;
    //                     }
    //                 }
    //             }
    //         }
        @endphp

        @if ($isVariantProduct)
            <a href="javascript:void(0);" class="btn btn-outline-secondary btn-sm border-secondary d-block mt-4 w-100 direct-add-to-cart-btn add-to-cart-text button_responsive"
               onclick="showProductDetailsModal({{ $product->id }})">{{ localize('Add to Cart') }}</a>
            @else
            <form action="" class="direct-add-to-cart-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="product_variation_id" value="{{ $product->variations[0]->id }}">
                <input type="hidden" value="1" name="quantity">


{{--wis list--}}
{{--                <div class="d-flex gap-2 align-content-between" style="vertical-align: center;flex-wrap: nowrap">--}}
                    @if (!$isVariantProduct && $product->productPrices[0]['stock_qty'] < 1)
                        <a href="javascript:void(0);"
                           class="btn btn-outline-secondary btn-sm border-secondary d-block mt-4 add-to-cart-text">{{ localize('Out of Stock') }}</a>

                     @else
{{--                        <div style="width: 80%">--}}
                            <a href="javascript:void(0);" onclick="directAddToCartFormSubmit(this)" class="btn btn-secondary btn-sm border d-block mt-4  direct-add-to-cart-btn add-to-cart-text">{{ localize('Add to Cart') }}</a>
{{--                        </div>--}}
                    @endif

{{--                    @if (Auth::check() && Auth::user()->user_type == 'customer')--}}
{{--                        <div style="width: 20%">--}}
{{--                        <a href="javascript:void(0);" class="rounded-btn" style="margin-top: 25px;"><i class="fa-regular fa-heart"--}}
{{--                                                                             onclick="addToWishlist({{ $product->id }})"></i></a>--}}
{{--                        </div>--}}
{{--                            @elseif(!Auth::check())--}}
{{--                        <div>--}}
{{--                        <a href="javascript:void(0);" class="rounded-btn" style="margin-top: 25px;"><i class="fa-regular fa-heart"--}}

{{--                                                                             onclick="addToWishlist({{ $product->id }})"></i></a>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--                //wis list--}}
            </form>
        @endif

    </div>
</div>
