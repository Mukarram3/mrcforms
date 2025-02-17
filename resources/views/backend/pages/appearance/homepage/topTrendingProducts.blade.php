@extends('backend.layouts.master')

@section('title')
    {{ localize('Website Homepage Configuration') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

<?php

$toptrendingProduct = \App\Models\TopTrendingProduct::query();
$branch = \App\Models\Branch::all();
$products = \App\Models\Product::select('id','name')->get();
?>

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Trending Products') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4">
                <!--left sidebar-->
                <div class="col-xl-9 order-2 order-md-2 order-lg-2 order-xl-1">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Select Trending Product For Branch') }}</h2>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('admin.trending.store') }}" method="POST" enctype="multipart/form-data" class="trending-products-form">
                        @csrf
                        <!--slider info start-->
                        @foreach($branch as $br)
                                <?php
                                $toptrending = \App\Models\TopTrendingProduct::where('branch_id', $br->id)->orderBy('created_at', 'desc')->first();
                                $topproduct = \App\Models\TopTrendingProduct::where('branch_id', $br->id)->orderBy('created_at', 'desc')->first();
                                $trending_cat = $toptrending ? json_decode($toptrending->category_id) : '';
//                                dd($topproduct);
                                $trending_pro = $toptrending ? json_decode($topproduct->products) : '';
//                                print_r($trending_cat);
                                ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="mb-4">
                                        <label class="form-label">{{ localize('Select Branch') }}</label>
                                        <select class="select2Max3 form-control" data-placeholder="{{ localize('Select Branch') }}" name="branch_ids[]" required>
                                            <option value="{{ $br->id }}" selected>{{ $br->name }}</option>
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">{{ localize('Categories') }}</label>
                                        <select class="select2Max3 form-control trending_product_categories" multiple="multiple"
                                                data-placeholder="{{ localize('Select categories') }}"
                                                name="trending_product_categories[{{ $br->id }}][]" >
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        @if(!empty($trending_cat))
                                                            @if(in_array($category->id, $trending_cat )) selected @endif
                                                        @endif>
                                                    {{ $category->collectLocalization('name') }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">{{ localize('Top Trending Products') }}</label>
                                        <input type="hidden" name="types[]" value="top_trending_products">
                                        <select class="select2 form-control top_trending_products" multiple="multiple"
                                                data-placeholder="{{ localize('Select products') }}" name="top_trending_products[{{ $br->id }}][]"
                                                required>

                                            @foreach ($products as $pd)
                                                <option value="{{ $pd->id }}"
                                                        @if(!empty($trending_pro))
                                                            @if(in_array($pd->id, $trending_pro )) selected @endif
                                                        @endif>
                                                    {{ $pd->name }}
                                                </option>
                                            @endforeach


                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--slider info end-->
                        @endforeach

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-4">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


{{--                Second Branch--}}
{{--                END--}}

{{--                Third Branch--}}
{{--                END--}}

                <!--right sidebar-->
                <div class="col-xl-3 order-1 order-md-1 order-lg-1 order-xl-2">
                    <div class="card tt-sticky-sidebar">
                        <div class="card-body">
                            <h5 class="mb-4">{{ localize('Homepage Configuration') }}</h5>
                            <div class="tt-vertical-step-link">
                                <ul class="list-unstyled">
                                    @include('backend.pages.appearance.homepage.inc.rightSidebar')
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script>
        "use strict";

        // runs when the document is ready --> for media files
        $(document).ready(function() {
            getChosenFilesCount();
            showSelectedFilePreviewOnLoad();
            // getProducts();
        });

        // //  get cities on state change
        // $(document).on('change', '.trending_product_categories', function() {
        //     getProducts();
        // });

        // get top trending products
        {{--function getProducts() {--}}
        {{--    $.ajax({--}}
        {{--        type: "POST",--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
        {{--        },--}}
        {{--        url: '{{ route('admin.appearance.homepage.getProducts') }}',--}}
        {{--        data: $('.trending-products-form').serialize(),--}}
        {{--        success: function(data) {--}}
        {{--            $('[name="city_id"]').html("");--}}
        {{--            $('.top_trending_products').html(JSON.parse(data));--}}
        {{--        }--}}
        {{--    });--}}
        {{--}--}}
    </script>
@endsection
