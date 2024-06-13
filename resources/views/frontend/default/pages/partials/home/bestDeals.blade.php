



<section class="pb-120 position-relative z-1 pt-120">
    <div class="container">
        <div class="row g-4 align-items-center justify-content-center">
            <div class="col-xxl-4 col-xl-5 order-2 order-xxl-1 d-none d-xl-block d-none-1399">
                <a href="{{ getSetting('best_deal_banner_link') }}">
                    <img src="{{ uploadedAsset(getSetting('best_deal_banner')) }}" alt="" class="img-fluid">
                </a>
            </div>
            <div class="col-xxl-8 order-1 order-xxl-2">
                <div
                    class="timing-box d-flex align-items-center justify-content-center justify-content-sm-between rounded-3 flex-wrap gap-3">
                    <h4 class="mb-0">{{ localize('Weekly Best Deals') }}</h4>
                    @php
                        $best_deal_end_date = getSetting('best_deal_end_date');
                        if (!is_null($best_deal_end_date)) {
                            $best_deal_end_date = date('m/d/Y H:i:s', strtotime($best_deal_end_date));
                        }
                    @endphp

                    <ul class="timing-countdown countdown-timer d-flex align-items-center gap-2"
                        data-date="{{ $best_deal_end_date }}">
                        <li
                            class="position-relative z-1 d-flex align-items-center justify-content-center flex-column rounded-2">
                            <h5 class="mb-0 days">00</h5>
                            <span class="gshop-subtitle fs-xxs d-block">{{ localize('Days') }}</span>
                        </li>
                        <li
                            class="position-relative z-1 d-flex align-items-center justify-content-center flex-column rounded-2">
                            <h5 class="mb-0 hours">00</h5>
                            <span class="gshop-subtitle fs-xxs d-block">{{ localize('Hours') }}</span>
                        </li>
                        <li
                            class="position-relative z-1 d-flex align-items-center justify-content-center flex-column rounded-2">
                            <h5 class="mb-0 minutes">00</h5>
                            <span class="gshop-subtitle fs-xxs d-block">{{ localize('Min') }}</span>
                        </li>
                        <li
                            class="position-relative z-1 d-flex align-items-center justify-content-center flex-column rounded-2">
                            <h5 class="mb-0 seconds">00</h5>
                            <span class="gshop-subtitle fs-xxs d-block">{{ localize('Sec') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="mt-4">
                    <div class="row g-4">
                        @php
                            $weekly_best_deals = getSetting('weekly_best_deals') != null ? json_decode(getSetting('weekly_best_deals')) : [];
//                            $products = \App\Models\Product::whereIn('id', $weekly_best_deals)->get();

                                      if (\Illuminate\Support\Facades\Session::has('city')) {
            $cityId = \Illuminate\Support\Facades\Session::get('city');

            $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
                $q->where('city_id', $cityId);
            })->latest()->first();

            $branchId = $branch->id ?? null;

            $products = \App\Models\Product::whereHas('productPrices', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })->with(['productPrices' => function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            }])->whereIn('id', $weekly_best_deals)->get();

        }else{
            $products = \App\Models\Product::whereIn('id', $weekly_best_deals)->get();
        }



                        @endphp

                        @foreach ($products as $product)
                            <div class="col-6  col-md-6 col-lg-6">
                                @include(
                                    'frontend.default.pages.partials.products.horizontal-product-card',
                                    [
                                        'product' => $product,
                                    ]
                                )
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




{{--All Products Home Page--}}
<section class="pt-8 pb-100 bg-white position-relative overflow-hidden z-1 trending-products-area">
    {{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/garlic.png') }}" alt="garlic"--}}
    {{--         class="position-absolute garlic z--1" data-parallax='{"y": 100}'>--}}
    {{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/carrot.png') }}" alt="carrot"--}}
    {{--         class="position-absolute carrot z--1" data-parallax='{"y": -100}'>--}}
    {{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/mashrom.png') }}" alt="mashrom"--}}
    {{--         class="position-absolute mashrom z--1" data-parallax='{"x": 100}'>--}}
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5">
                <div class="section-title text-center text-xl-start">
                    <h3 class="mb-0">{{ localize('All Products') }}</h3>
                </div>
            </div>
            <div class="col-xl-7">
                {{--                <div class="filter-btns gshop-filter-btn-group text-center text-xl-end mt-4 mt-xl-0">--}}

                {{--                    @php--}}
                {{--                        $trending_product_categories = getSetting('trending_product_categories') != null ? json_decode(getSetting('trending_product_categories')) : [];--}}
                {{--                        $categories = \App\Models\Category::whereIn('id', $trending_product_categories)->get();--}}
                {{--                    @endphp--}}
                {{--                    <button class="active" data-filter="*">{{ localize('All Products') }}</button>--}}
                {{--                    @foreach ($categories as $category)--}}
                {{--                        <button data-filter=".{{ $category->id }}">{{ $category->collectLocalization('name') }}</button>--}}
                {{--                    @endforeach--}}



                {{--                </div>--}}
            </div>
        </div>

        @php
        @endphp
        <div class="row g-4" id="allProducts">
            <div class="col-12" style="display: none" id="skelton">
                <div class="row">

            @for($i=1 ; $i<=12 ; $i++)
                <div class="col-lg-3 col-6 pt-3" >
                    <div class="card" aria-hidden="true">
                        <span class="placeholder col-12" style="height: 300px"></span>
                        <div class="card-body">
                            <h5 class="card-title placeholder-glow">
                                <span class="placeholder col-6"></span>
                            </h5>
                            <p class="card-text placeholder-glow">
                                <span class="placeholder col-7"></span>
                                <span class="placeholder col-4"></span>
                                <span class="placeholder col-4"></span>
                            </p>
                        </div>
                    </div>
                </div>

            @endfor
            </div>
            </div>
        </div>
        <div class="row pt-4">
            <div class="col-12 text-center">
                <button class="btn btn-outline-secondary  border-secondary" id="loadmore">
                    <span class="spinner-border "  role="status"  id="spinner" style="height: 20px;width: 20px;display: none" >
                        <span class="visually-hidden"></span>
                    </span>
                    Load More
                </button>
            </div>
        </div>
    </div>
</section>




