<style>
    @media only screen and (max-width:420px){
        #image_responsive{
            width: 100% !important;
            height:100px !important;
        }
    }

    @media only screen and (min-width:420px){
        #image_responsive{
            width: 100% !important;
            height:250px !important;
        }
    }


</style>
<div class="vertical-product-card rounded-2 position-relative swiper-slide {{ isset($bgClass) ? $bgClass : '' }}">

    @php
        $discountPercentage = discountPercentage($product);
    @endphp

    @if ($discountPercentage > 0)
        <span class="offer-badge text-white fw-bold fs-xxs bg-danger position-absolute start-0 top-0">
            -{{ discountPercentage($product) }}% <span class="text-uppercase">{{ localize('Off') }}</span>
        </span>
    @endif

    <a href="{{ route('products.show', $product->slug) }}">
        <div class="thumbnail position-relative text-center p-4">

        <img src="{{ uploadedAsset($product->thumbnail_image) }}" alt="{{ $product->collectLocalization('name') }}"
            class="img-fluid" id="image_responsive">
        <div class="product-btns position-absolute d-flex gap-2 flex-column">

            @if (Auth::check() && Auth::user()->user_type == 'customer')
                <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"
                        onclick="addToWishlist({{ $product->id }})"></i></a>
            @elseif(!Auth::check())
                <a href="javascript:void(0);" class="rounded-btn"><i class="fa-regular fa-heart"
                        onclick="addToWishlist({{ $product->id }})"></i></a>
            @endif

            <a href="javascript:void(0);" class="rounded-btn" onclick="showProductDetailsModal({{ $product->id }})"><i
                    class="fa-regular fa-eye"></i></a>
        </div>
        </div>
    </a>

    <div class="card-content">
        <!--product category start-->
        <div class="mb-2 tt-category tt-line-clamp tt-clamp-1">
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
            class="card-title fw-bold mb-2 tt-line-clamp tt-clamp-1">{{ $product->collectLocalization('name') }}
        </a>

        <h6 class="price">
            @include('frontend.default.pages.partials.products.pricing', [
                'product' => $product,
                'onlyPrice' => true,
            ])
        </h6>

        @isset($showSold)
            <div class="card-progressbar mt-3 mb-2 rounded-pill">
                <span class="card-progress bg-primary" data-progress="{{ sellCountPercentage($product) }}%"
                    style="width: {{ sellCountPercentage($product) }}%;"></span>
            </div>
            <p class="mb-0 fw-semibold">{{ localize('Total Sold') }}: <span
                    class="fw-bold text-secondary">{{ $product->total_sale_count }}/{{ $product->sell_target }}</span>
            </p>
        @endisset
    </div>
    <div class="card-btn bg-white">

        @php
            $isVariantProduct = 0;
            $stock = 0;
            if ($product->variations()->count() > 1) {
                $isVariantProduct = 1;
            } else {

                   $cityid = \Illuminate\Support\Facades\Session::get('city');
        $branch=\App\Models\Branch::whereHas('cities',function($q) use ($cityid){
            return $q->where('city_id',$cityid);
        })->get()->last();
        if ($branch){
            $productPrice=\App\Models\ProductPrice::where('branch_id',$branch->id)
                ->where('product_id',$product->id)->first();
            if ($productPrice) {
                $stock = $productPrice->stock_qty;
//                dd($price);
            }else{

//             $stock = $product->variations[0]->product_variation_stock ? $product->variations[0]->product_variation_stock->stock_qty : 0;
             $stock = 0;

            }
        }else{
//           $stock = $product->variations[0]->product_variation_stock ? $product->variations[0]->product_variation_stock->stock_qty : 0;
           $stock =  0;
        }

            }
        @endphp

        @if ($isVariantProduct)
            <a href="javascript:void(0);" class="btn btn-secondary d-block btn-sm btn-md rounded-1 w-100 direct-add-to-cart-btn add-to-cart-text"
                onclick="showProductDetailsModal({{ $product->id }})">{{ localize('Add to Cart') }}</a>
        @else
            <form action="" class="direct-add-to-cart-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="product_variation_id" value="{{ $product->variations[0]->id }}">
                <input type="hidden" value="1" name="quantity">

                @if (!$isVariantProduct && $stock < 1)
                    <a href="javascript:void(0);" class="btn btn-secondary d-block btn-sm btn-md rounded-1 w-100 direct-add-to-cart-btn add-to-cart-text">
                        {{ localize('Out of Stock') }}</a>
                @else
                    <a href="javascript:void(0);" onclick="directAddToCartFormSubmit(this)"
                        class="btn btn-secondary d-block btn-sm btn-md rounded-1 w-100 direct-add-to-cart-btn add-to-cart-text">{{ localize('Add to Cart') }}</a>
                @endif
            </form>
        @endif
    </div>
</div>
