@extends('backend.layouts.master')

@section('title')
    {{ localize('Reviews ') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Products') }}</h2>
                            </div>
                            <div class="tt-action">
                                @can('add_products')
                                    <button type="button" class="btn multiple-delete  btn-danger">
                                        <div class="spinner-border" id="loader" role="status" style="height: 18px;width: 18px;display: none">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Delete Selected Product</button>


                                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary"><i
                                                data-feather="plus"></i> {{ localize('Add Product') }}</a>
                                @endcan
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
                                <div class="row justify-content-between g-2">
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select select2" name="brand_id">
                                                <option value="">{{ localize('Select Brand') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                    </div>
                    </form>

                    <table class="table tt-footable border-top" id="myTable" data-use-parent-width="true">
                        <tr>
                            <th>
                                <input type="checkbox" class="master myCheckbox" id="myCheckbox"/>
                            </th>
                            <th class="text-center">{{ localize('S/L') }}</th>
                            <th>{{ localize('Product Name') }}</th>
                            <th data-breakpoints="xs sm">{{ localize('Customer') }}</th>
                            <th data-breakpoints="xs sm">{{ localize('Comment') }}</th>
                            <th data-breakpoints="xs sm">{{ localize('Images') }}</th>
                           <th data-breakpoints="xs sm md">{{ localize('Published') }}</th>
                        </tr>
                        <tbody>

                        @foreach ($reviews as $key => $review)
                            <tr>
                                <td><input type="checkbox" class="record-checkbox" value="{{ $review->id }}" ></td>
                                <td class="text-center">
                                    {{ $key + 1 + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                <td>
                                    @if($review->product)
                                        <a href="{{ route('products.show', $review->product->slug) }}"
                                           class="d-flex align-items-center" target="_blank">
                                            <div class="avatar avatar-sm">
                                                <img class="rounded-circle"
                                                     src="{{ uploadedAsset($review->product->thumbnail_image) }}" alt=""
                                                     onerror="this.onerror=null;this.src='{{ staticAsset('backend/assets/img/placeholder-thumb.png') }}';" />
                                            </div>
                                            <h6 class="fs-sm mb-0 ms-2">{{ $review->product->name  }}
                                            </h6>
                                        </a>
                                    @endif
                                </td>
                                <td> <p>Name : {{  $review->user->name }}<br>
                                        Email : {{ $review->user->email }} <br>
                                        Phone : {{ $review->user->phone }} <br>
                                        Rating : @for($i=1;$i<=$review->rating;$i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="orange" height="16" width="18" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>
                                        @endfor
                                    </p>
                                </td>
                                <td>
                                    {{ $review->comment }}
                                </td>
                                <td>
                                    @foreach($review->reviewImage as $img)
                                        <img src="{{ staticAsset($img->images) }}" style="height: 60px;width: 60px">
                                    @endforeach
                                </td>
                                 <td>
                                    @can('publish_products')
                                        <div class="form-check form-switch">
                                            <input type="checkbox" onchange="updatePublishedStatus(this)"
                                                   class="form-check-input"
                                                   @if ($review->status == 'active') checked @endif
                                                   value="{{ $review->id }}">
                                        </div>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <!--pagination start-->
                    <div class="d-flex align-items-center justify-content-between px-4 pb-4">
                            <span>{{ localize('Showing') }}
                                {{ $reviews->firstItem() }}-{{ $reviews->lastItem() }} {{ localize('of') }}
                                {{ $reviews->total() }} {{ localize('results') }}</span>
                        <nav>
                            {{ $reviews->appends(request()->input())->links() }}
                        </nav>
                    </div>
                    <!--pagination end-->
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection




@section('scripts')
    <script>
        "use strict"


        // update feature status
        function updateFeatureStatus(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('admin.products.updateFeatureStatus') }}', {
                    _token: '{{ csrf_token() }}',
                    id: el.value,
                    status: status
                },
                function(data) {
                    if (data == 1) {
                        notifyMe('success', '{{ localize('Status updated successfully') }}');
                    } else {
                        notifyMe('danger', '{{ localize('Something went wrong') }}');
                    }
                });
        }

        // update publish status
        function updatePublishedStatus(el) {
            if (el.checked) {
                var status = 'active';
            } else {
                var status = 'inactive';
            }
            $.post('{{ route('admin.review.updatePublishedStatus') }}', {
                    _token: '{{ csrf_token() }}',
                    id: el.value,
                    status: status
                },
                function(data) {
                    if (data == 1) {
                        notifyMe('success', '{{ localize('Status updated successfully') }}');
                    } else {
                        notifyMe('danger', '{{ localize('Something went wrong') }}');
                    }
                });
        }
    </script>


    <script>
        $(document).ready(function() {
            $('#myTable').on('change', '.myCheckbox', function() {
                if($(this).is(':checked',true))
                {
                    console.log("helo world ");
                    $(".record-checkbox").prop('checked', true);
                } else {
                    $(".record-checkbox").prop('checked',false);
                }
            });

            $('.multiple-delete').on('click',function (){
                var selectedIds = [];
                $('.record-checkbox:checked').each(function () {
                    selectedIds.push($(this).val());
                });
                console.log(selectedIds);
                if (selectedIds.length > 0) {
                    window.alert("Are You Sure To Delete Selected Items")
                    $('#loader').show();
                    $.ajax({
                        type: 'GET',
                        url: '{{ route('admin.products.delete.ids') }}',
                        data:{'ids':selectedIds},
                        dataType: 'JSON',
                        success: function (results) {
                            console.log(results);
                            $('#loader').hide();
                            if (results.status == 'success') {
                                window.alert('Product Delete Success ...')
                                window.location.reload();
                            } else {
                                window.alert("SomeThings Went Wrong .....");
                            }
                        }
                    });
                }else{
                    window.alert("Please Select Product ..... ")
                }
            })

        });

    </script>
@endsection

