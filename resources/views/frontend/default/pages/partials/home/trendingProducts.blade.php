<section class="pt-4 pb-100 bg-white position-relative overflow-hidden z-1 trending-products-area">
{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/garlic.png') }}" alt="garlic"--}}
{{--        class="position-absolute garlic z--1" data-parallax='{"y": 100}'>--}}
{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/carrot.png') }}" alt="carrot"--}}
{{--        class="position-absolute carrot z--1" data-parallax='{"y": -100}'>--}}
{{--    <img src="{{ staticAsset('frontend/default/assets/img/shapes/mashrom.png') }}" alt="mashrom"--}}
{{--        class="position-absolute mashrom z--1" data-parallax='{"x": 100}'>--}}
    <div class="container">
        <div class="row align-items-center">
            <div class="col-xl-5">
                <div class="section-title text-center text-xl-start">
                    <h3 class="mb-0">{{ localize('Top Trending Products') }}</h3>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="filter-btns gshop-filter-btn-group text-center text-xl-end mt-2 mt-xl-0">

                    @php
                        $trending_product_categories = getSetting('trending_product_categories') != null ? json_decode(getSetting('trending_product_categories')) : [];
                        $categories = \App\Models\Category::whereIn('id', $trending_product_categories)->get();
                    @endphp
{{--                    <button class="active" data-filter="*">{{ localize('All Products') }}</button>--}}
                    @foreach ($categories as $category)
                        <button data-filter=".{{ $category->id }}">{{ $category->collectLocalization('name') }}</button>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row g-4 filter_group">

            @php
                    $trending_products = getSetting('top_trending_products') != null ? json_decode(getSetting('top_trending_products')) : [];
                    if (\Illuminate\Support\Facades\Session::has('city')) {
                    $cityId = \Illuminate\Support\Facades\Session::get('city');

                    $branch = \App\Models\Branch::whereHas('cities', function ($q) use ($cityId) {
                        $q->where('city_id', $cityId);
                    })->latest()->first();

                    $branchId = $branch->id ?? null;

                    $trend = \App\Models\TopTrendingProduct::where('branch_id',$branchId)->first();
                    $trending_products = $trend != null ? json_decode($trend->products) : [];
                    $products = \App\Models\Product::whereHas('productPrices', function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    })->with(['productPrices' => function ($query) use ($branchId) {
                        $query->where('branch_id', $branchId);
                    }])->whereIn('id', $trending_products)->get();
                }else{
                    $products = \App\Models\Product::whereIn('id', $trending_products)->get();
                }
           @endphp

            @foreach ($products as $product)
                <div class="col-lg-3 col-6 pt-sm-2 pt-md-4 pt-lg-8  filter_item
                    @php
                       if($product->categories()->count() > 0){
                            foreach ($product->categories as $category) {
                               echo $category->id .' ';
                               echo $category->id .' ';
                            }
                        } @endphp">
                    @include('frontend.default.pages.partials.products.vertical-product-card', [
                        'product' => $product,
                    ])
                </div>
            @endforeach
        </div>
    </div>
</section>
