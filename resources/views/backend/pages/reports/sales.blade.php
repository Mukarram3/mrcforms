@extends('backend.layouts.master')

@section('title')
    {{ localize('Sales Report') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Product Sales Report') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <div class="card mb-4" id="section-1">
                        <form class="app-search" action="{{ Request::fullUrl() }}" method="GET">
                            <div class="card-header border-bottom-0">
                                <div class="row justify-content-between g-3">
                                    <div class="col-auto flex-grow-1">
                                        <div class="tt-search-box">
                                            <div class="input-group">
                                                <span class="position-absolute top-50 start-0 translate-middle-y ms-2"> <i
                                                        data-feather="search"></i></span>
                                                <input class="form-control rounded-start w-100" type="text"
                                                    id="search" name="search" placeholder="{{ localize('Search') }}"
                                                    @isset($searchKey)
                                                value="{{ $searchKey }}"
                                                @endisset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select select2" name="order"
                                                data-minimum-results-for-search="Infinity">
                                                <option value="DESC"
                                                    @isset($order)
                                                         @if ($order == 'DESC') selected @endif
                                                        @endisset>
                                                    {{ localize('High ⟶ Low') }}</option>

                                                <option value="ASC"
                                                    @isset($order)
                                                         @if ($order == 'ASC') selected @endif
                                                        @endisset>
                                                    {{ localize('Low ⟶ High') }}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">
                                            <i data-feather="search" width="18"></i>
                                            {{ localize('Search') }}
                                        </button>
                                    </div>

{{--                                    Download report--}}
                                    <div class="col-auto">
                                        <a class="btn btn-primary" href="{{ route('admin.product.report.download') }}" role="button">

                                            {{ localize('Download Report') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table tt-footable border-top" data-use-parent-width="true">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ localize('S/L') }}
                                    </th>
                                    <th>{{ localize('Product Name') }}</th>
                                    <th>{{ localize('Barcode') }}</th>
                                    <th class="text-end">{{ localize('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $key => $product)
                                    <tr>
                                        <td class="text-center">
                                            {{ $key + 1 + ($products->currentPage() - 1) * $products->perPage() }}</td>
                                        <td>
                                            <a href="{{ route('products.show', $product->slug) }}"
                                                class="d-flex align-items-center" target="_blank">
                                                <div class="avatar avatar-sm">
                                                    <img class="rounded-circle"
                                                        src="{{ uploadedAsset($product->thumbnail_image) }}" alt=""
                                                        onerror="this.onerror=null;this.src='{{ staticAsset('backend/assets/img/placeholder-thumb.png') }}';" />
                                                </div>
                                                <h6 class="fs-sm mb-0 ms-2">{{ $product->collectLocalization('name') }}
                                                </h6>
                                            </a>
                                        </td>

                                        <td class="">
                                            <?php $bar_code = \App\Models\ProductPrice::where('product_id',$product->id)->first() ? \App\Models\ProductPrice::where('product_id',$product->id)->first()->bar_code : ''  ?>
                                            {{  $bar_code }}
                                        </td>

                                        <td class="text-end">
                                            {{ $product->total_sale_count }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--pagination start-->
                        <div class="d-flex align-items-center justify-content-between px-4 pb-4">
                            <span>{{ localize('Showing') }}
                                {{ $products->firstItem() }}-{{ $products->lastItem() }} {{ localize('of') }}
                                {{ $products->total() }} {{ localize('results') }}</span>
                            <nav>
                                {{ $products->appends(request()->input())->links() }}
                            </nav>
                        </div>
                        <!--pagination end-->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
