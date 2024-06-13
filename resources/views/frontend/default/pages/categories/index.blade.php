@extends('frontend.default.layouts.master')

@section('title')
    {{ localize('Categories') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection
<?php
$categories = \App\Models\Category::with('childrenCategories')->where('level',0)->get();
?>

@section("css")
    <style>
       .gshop-footer {
            display: none !important;
        }
       .nav-pills .nav-link.collapsed::after {
           content: "\25BE"; /* Unicode for down arrow */
       }
       .nav-pills .nav-link:not(.collapsed)::after {
           content: "\25B4"; /* Unicode for up arrow */
       }
    </style>
    @endsection
@section('contents')
    <section class="tt-campaigns">
        <div class="container">
            <div class="row pt-1">
                <!-- Vertical Nav tabs -->
                <div class="col-12 px-0 mx-0 col-md-4" style="height: 100vh;overflow: scroll">
                    <div class="nav flex-column nav-pills background-color" id="v-pills-tab"  role="tablist" aria-orientation="vertical">
                        @foreach($categories as $key=> $category)
                            <a href="{{ route('products.index') }}?&category_id={{ $category->id }}">{{ $category->name }}
                            <div class="nav-link nav-link px-4 py-3 @if($loop->first) show active @endif text-center" style="font-size: 12px;width: 100%" id="v-pills-{{ $category->id }}-tab" data-bs-toggle="pill" href="#v-pills-{{ $category->id }}">
                                <div class="avatar avatar sm" style="width: 1.75rem;height: 1.75rem;line-height: 1.75rem;display: inline-block; margin-right: 10px;">
                                    <img class="rounded-circle" style="width: 100%;height: 100%;object-fit: contain;"
                                         src="{{ uploadedAsset($category->collectLocalization('thumbnail_image')) }}"
                                         alt="" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'" />
                                </div>
                                    <i class="fa fa-caret-right fa-thin custome_font"></i>
                            </div>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Tab panes -->
                <div class="col-8 col-md-8 d-none" style="height: 100vh;overflow: scroll">
                    <div class="tab-content" id="v-pills-tabContent">
                        @foreach($categories as $category)
                            <div class="tab-pane fade @if($loop->first) show active @endif" id="v-pills-{{ $category->id }}" role="tabpanel" aria-labelledby="v-pills-{{ $category->id }}-tab">
                                <!-- Accordion -->
                                <div class="accordion" id="accordion{{ $category->id }}">
                                    @foreach($category->childrenCategories as $item) <!-- Assuming 'items' represents related items -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" style="font-size: 12px" id="heading{{ $item->id }}">
                                            <button class="accordion-button" style="font-size: 12px" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $item->id }}" aria-expanded="true" aria-controls="collapse{{ $item->id }}">
                                                {{ $item->name }}
                                            </button>
                                        </h2>
                                        <div id="collapse{{ $item->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $item->id }}" data-bs-parent="#accordion{{ $category->id }}">
                                            <div class="accordion-body">
                                                @if($item->childrenCategories->isNotEmpty())
                                                    @foreach($item->childrenCategories->chunk(3) as $chunk)
                                                        <div class="row">
                                                            @foreach($chunk as $sub_cate)
                                                                <div class="col-sm-12 col-md-6 col-lg-4">
                                                                    <div class="list-group my-1">
                                                                        <a href="{{ route('products.index') }}?&category_id={{ $sub_cate->id }}" class="list-group-item list-group-item-action" style="font-size: 12px">{{ $sub_cate->name }}</a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

{{--            old category--}}
{{--            <div class="row">--}}
{{--                @php--}}
{{--                    $categories = \App\Models\Category::where('level', 0)->orderBy('id', 'desc')->get();--}}
{{--                @endphp--}}

{{--                <div class="col-lg-12">--}}
{{--                    <div class="row">--}}
{{--                        @foreach($categories->chunk(2) as $categoryChunk)--}}
{{--                            <div class="col-lg-6 mb-4">--}}
{{--                                <div class="accordion" id="accordionFlush">--}}
{{--                                    @foreach($categoryChunk as $category)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-headingOne{{$category->id}}">--}}
{{--                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne-{{ $category->id }}" aria-expanded="false" aria-controls="flush-collapseOne-{{ $category->id }}">--}}
{{--                                                    <div class="row g-0 align-items-center">--}}
{{--                                                        <div class="col-3 py-1">--}}
{{--                                                            <img src="{{ uploadedAsset($category->thumbnail_image) }}" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'" style="height: 50px;width: 50px" class="img-fluid rounded-start" alt="...">--}}
{{--                                                        </div>--}}
{{--                                                        <div class="col-9 ps-lg-4 ps-2">{{ $category->name }}</div>--}}
{{--                                                    </div>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapseOne-{{ $category->id }}" class="accordion-collapse collapse" aria-labelledby="flush-headingOne{{ $category->id }}" data-bs-parent="#accordionFlush">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    @php--}}
{{--                                                        $subCategories = \App\Models\Category::where('parent_id', $category->id)->get();--}}
{{--                                                    @endphp--}}
{{--                                                    <div class="row">--}}
{{--                                                        @foreach($subCategories->chunk(2) as $subChunk)--}}
{{--                                                            @foreach($subChunk as $sub)--}}
{{--                                                                <div class="col-md-6 mb-2">--}}
{{--                                                                    <li style="font-size: 14px"><a href="{{ route('products.index') }}?&category_id={{ $sub->id }}" class="hover-primary">{{ $sub->name }}</a></li>--}}
{{--                                                                </div>--}}
{{--                                                            @endforeach--}}
{{--                                                        @endforeach--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endforeach--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
        </div>
{{--        end category--}}
    </section>
    <!--campaign section end-->






    {{--                        <div class="card2 card mb-3" style="max-width: 540px;">--}}
    {{--                            <div class="row g-0 align-items-center">--}}
    {{--                                <div class="col-3 py-1">--}}
    {{--                                    <img src="{{ uploadedAsset($category->thumbnail_image) }}" onerror="this.src='{{ staticAsset('backend/assets/img/default_image.jpeg') }}'" class="img-fluid rounded-start" alt="...">--}}
    {{--                                </div>--}}
    {{--                                <div class="col-9">--}}
    {{--                                    <div class="card-body">--}}
    {{--                                        <h6 style="font-size: 12px">{{ $category->name }}</h6>--}}
    {{--                                        @php--}}
    {{--                                            $subCategories = \App\Models\Category::where('parent_id', $category->id)->get();--}}
    {{--                                        @endphp--}}
    {{--                                        <ul>--}}
    {{--                                            @foreach($subCategories->chunk(4) as $subChunk)--}}
    {{--                                                <div class="row">--}}
    {{--                                                    @foreach($subChunk as $sub)--}}
    {{--                                                        <div class="col-md-3">--}}
    {{--                                                            <li> {{ $sub->name }}</li>--}}
    {{--                                                        </div>--}}
    {{--                                                    @endforeach--}}
    {{--                                                </div>--}}
    {{--                                            @endforeach--}}
    {{--                                        </ul>--}}
    {{--                                    </div>--}}
    {{--                                </div>--}}
    {{--                            </div>--}}
    {{--                        </div>--}}
@endsection



