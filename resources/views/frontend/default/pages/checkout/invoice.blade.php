@extends('frontend.default.layouts.master')

@section('title')
    {{ localize('Invoice') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('css')
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    <style type="text/css">
        /*
            Use :not with impossible condition so inputs are only hidden
            if pseudo selectors are supported. Otherwise the user would see
            no inputs and no highlighted stars.
        */
        .rating input[type="radio"]:not(:nth-of-type(0)) {
            /* hide visually */
            border: 0;
            clip: rect(0 0 0 0);
            height: 1px;
            margin: -1px;
            overflow: hidden;
            padding: 0;
            position: absolute;
            width: 1px;
        }
        .rating [type="radio"]:not(:nth-of-type(0)) + label {
            display: none;
        }

        label[for]:hover {
            cursor: pointer;
        }

        .rating .stars label:before {
            content: "★";
        }

        .stars label {
            color: lightgray;
            font-size: 36px;
        }

        .stars label:hover {
            text-shadow: 0 0 1px #000;
        }

        .rating [type="radio"]:nth-of-type(1):checked ~ .stars label:nth-of-type(-n+1),
        .rating [type="radio"]:nth-of-type(2):checked ~ .stars label:nth-of-type(-n+2),
        .rating [type="radio"]:nth-of-type(3):checked ~ .stars label:nth-of-type(-n+3),
        .rating [type="radio"]:nth-of-type(4):checked ~ .stars label:nth-of-type(-n+4),
        .rating [type="radio"]:nth-of-type(5):checked ~ .stars label:nth-of-type(-n+5) {
            color: orange;
        }

        .rating [type="radio"]:nth-of-type(1):focus ~ .stars label:nth-of-type(1),
        .rating [type="radio"]:nth-of-type(2):focus ~ .stars label:nth-of-type(2),
        .rating [type="radio"]:nth-of-type(3):focus ~ .stars label:nth-of-type(3),
        .rating [type="radio"]:nth-of-type(4):focus ~ .stars label:nth-of-type(4),
        .rating [type="radio"]:nth-of-type(5):focus ~ .stars label:nth-of-type(5) {
            color: darkorange;
        }
    </style>
    @endsection

@section('contents')
    <!--invoice section start-->
    @if (!is_null($orderGroup))
        @php
            $order = $orderGroup->order;
            $orderItems = $order->orderItems;
        @endphp
        <section class="invoice-section pt-6 pb-120">
            <div class="container">
                <div class="invoice-box bg-white rounded p-4 p-sm-6">
                    <div class="row g-5 justify-content-between">
                        <div class="col-lg-6">
                            <div class="invoice-title d-flex align-items-center">
                                <h3>{{ localize('Invoice') }}</h3>
                                <span class="badge rounded-pill bg-primary-light text-primary fw-medium ms-3">
                                    {{ ucwords(str_replace('_', ' ', $order->delivery_status)) }}
                                </span>
                            </div>
                            <table class="invoice-table-sm">
                                <tr>
                                    <td><strong>{{ localize('Order Code') }}</strong></td>
                                    <td>{{ getSetting('order_code_prefix') }}{{ $orderGroup->order_code }}</td>
                                </tr>

                                <tr>
                                    <td><strong>{{ localize('Date') }}</strong></td>
                                    <td>{{ date('d M, Y', strtotime($orderGroup->created_at)) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-5 col-md-8">
                            <div class="text-lg-end">
                                <a href="{{ route('home') }}"><img src="{{ uploadedAsset(getSetting('navbar_logo')) }}"
                                        alt="logo" class="img-fluid"></a>
                                <h6 class="mb-0 text-gray mt-4">{{ getSetting('site_address') }}</h6>
                            </div>
                        </div>
                    </div>
                    <span class="my-6 w-100 d-block border-top"></span>
                    <div class="row justify-content-between g-5">
                        <div class="col-xl-7 col-lg-6">
                            <div class="welcome-message">
                                <h4 class="mb-2">{{ auth()->user()->name }}</h4>
                                <p class="mb-0">
                                    {{ localize('Here are your order details. We thank you for your purchase.') }}</p>

                                @php
                                    $deliveryInfo = json_decode($order->scheduled_delivery_info);
                                @endphp

                                <p class="mb-0">{{ localize('Delivery Type') }}:
                                    <span
                                        class="badge bg-primary">{{ Str::title(Str::replace('_', ' ', $order->shipping_delivery_type)) }}</span>


                                </p>
                                @if ($order->shipping_delivery_type == getScheduledDeliveryType())
                                    <p class="mb-0">
                                        {{ localize('Delivery Time') }}:
                                        {{ date('d F', $deliveryInfo->scheduled_date) }},
                                        {{ $deliveryInfo->timeline }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-6">
                            @if (!$order->orderGroup->is_pos_order)
                                <div class="shipping-address d-flex justify-content-md-end">
                                    <div class="border-end pe-2">
                                        <h6 class="mb-2">{{ localize('Shipping Address') }}</h6>
                                        @php
                                            $shippingAddress = $orderGroup->shippingAddress;
                                        @endphp
                                        <p class="mb-0">{{ optional($shippingAddress)->address }},
                                            {{ optional(optional($shippingAddress)->city)->name }},
                                            {{ optional(optional($shippingAddress)->country)->name }}
                                            <br>
                                            {{ optional($shippingAddress)->phone }},
                                        </p>
                                    </div>
{{--                                    <div class="ms-4">--}}
{{--                                        <h6 class="mb-2">{{ localize('Billing Address') }}</h6>--}}
{{--                                        @php--}}
{{--                                            $billingAddress = $orderGroup->billingAddress;--}}
{{--                                        @endphp--}}
{{--                                        <p class="mb-0">{{ optional($billingAddress)->address }},--}}
{{--                                            {{ optional(optional($billingAddress)->city)->name }},--}}
{{--                                            {{ optional(optional($billingAddress)->state)->name }},--}}
{{--                                            {{ optional(optional($billingAddress)->country)->name }}</p>--}}
{{--                                    </div>--}}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive mt-6">
                        <table class="table invoice-table">
                            <tr>
                                <th>{{ localize('S/L') }}</th>
                                <th>{{ localize('Products') }}</th>
                                @if($order->delivery_status == "delivered")
                                    <th>{{ localize('Write Review') }}</th>
                                @endif
                                <th>{{ localize('U.Price') }}</th>
                                <th>{{ localize('QTY') }}</th>
                                <th>{{ localize('T.Price') }}</th>
                                @if($order->delivery_status == "delivered")
                                    <th></th>
                                @endif
                            </tr>
                            @foreach ($orderItems as $key => $item)
                                @php
                                    $product = $item->product_variation->product;
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td class="text-nowrap">
                                        <div class="d-flex">
                                            <img src="{{ uploadedAsset($product->thumbnail_image) }}"
                                                alt="{{ $product->collectLocalization('name') }}"
                                                class="img-fluid product-item d-none">
                                            {{-- <div class="ms-2"> --}}
                                            <div class="">
                                                <span>{{ $product->name }}</span>
                                                @if($order->delivery_status == "delivered")
                                                    <td class="text-secondary">
                                                    @if(checkReview($product->id) == 'false')
                                                        <button  class="text-secondary writeReview"  id="writeReview" data-product-id="{{ $product->id }}">{{ localize('Write Review') }}</button>
                                                        @else
                                                            <button  class="text-secondary writeReview" disabled>Reviewed</button>
                                                        @endif
                                                    </td>
                                                @endif
                                                <div>
                                                    @foreach (generateVariationOptions($item->product_variation->combinations) as $variation)
                                                        <span class="fs-xs">
                                                            {{ $variation['name'] }}:
                                                            @foreach ($variation['values'] as $value)
                                                                {{ $value['name'] }}
                                                            @endforeach
                                                            @if (!$loop->last)
                                                                ,
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ formatPrice($item->unit_price) }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ formatPrice($item->total_price) }}</td>

                                    @if($order->delivery_status == "delivered" && checkRefundItem($item->id,$order->id) == 'false')
                                        <th>
                                            <button  class="text-secondary refund-order"    data-item-id="{{ $item->id }}" data-product-qty="{{ $item->qty }}" data-product-id="{{ $product->id }}" data-order-id="{{ $order->id }}" >{{ localize('Refund Order') }}</button>
                                        </th>
                                    @endif
                                </tr>
                            @endforeach
                        </table>
                    </div>
                    <div class="mt-4 table-responsive">
                        <table class="table footer-table">
                            <tr>
                                <td>
                                    <strong class="text-dark d-block text-nowrap">{{ localize('Payment Method') }}</strong>
                                    <span> {{ ucwords(str_replace('_', ' ', $orderGroup->payment_method)) }}</span>
                                </td>

                                <td>
                                    <strong class="text-dark d-block text-nowrap">{{ localize('Sub Total') }}</strong>
                                    <span>{{ formatPrice($orderGroup->sub_total_amount) }}</span>
                                </td>
                                <td>
                                    <strong class="text-dark d-block text-nowrap">{{ localize('Shipping Cost') }}</strong>
                                    <span>{{ formatPrice($orderGroup->total_shipping_cost) }}</span>
                                </td>
                                @if ($orderGroup->total_coupon_discount_amount > 0)
                                    <td>
                                        <strong
                                            class="text-dark d-block text-nowrap">{{ localize('Coupon Discount') }}</strong>
                                        <span>{{ formatPrice($orderGroup->total_coupon_discount_amount) }}</span>
                                    </td>
                                @endif

                                <td>
                                    <strong class="text-dark d-block text-nowrap">{{ localize('Total Price') }}</strong>
                                    <span
                                        class="text-primary fw-bold">{{ formatPrice($orderGroup->grand_total_amount) }}</span>
                                </td>

                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="staticBackdrop"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Write A Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                            <div class="alert alert-primary" role="alert" id="update-alert" style="display: none">
                                You have already reviewed this product. Please update your review.
                            </div>



                        <form action="{{ route('product.review') }}" method="POST" enctype="multipart/form-data" id="product-review">
                            @csrf
                            <div class="row">
                                <div class="col-12">

                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <fieldset class="rating">
                                            <input id="demo-1" type="radio" name="rating" value="1">
                                            <label for="demo-1">1 star</label>
                                            <input id="demo-2" type="radio" name="rating" value="2">
                                            <label for="demo-2">2 stars</label>
                                            <input id="demo-3" type="radio" name="rating" value="3">
                                            <label for="demo-3">3 stars</label>
                                            <input id="demo-4" type="radio" name="rating" value="4">
                                            <label for="demo-4">4 stars</label>
                                            <input id="demo-5" type="radio" name="rating" value="5">
                                            <label for="demo-5">5 stars</label>
                                            <div class="stars">
                                                <label for="demo-1" aria-label="1 star" title="1 star"></label>
                                                <label for="demo-2" aria-label="2 stars" title="2 stars"></label>
                                                <label for="demo-3" aria-label="3 stars" title="3 stars"></label>
                                                <label for="demo-4" aria-label="4 stars" title="4 stars"></label>
                                                <label for="demo-5" aria-label="5 stars" title="5 stars"></label>
                                            </div>

                                        </fieldset>
                                 </div>
                                    <div class="mb-3">
                                        <label class="form-label">Comment</label>
                                        <textarea class="form-control" name="comment" placeholder="Enter Comment"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="input-field">
                                            <label class="form-label">Photos</label>
{{--                                            <div class="dropzone dropzone-default dropzone-primary dz-clickable"--}}
{{--                                                 id="kt_dropzone_2">--}}
{{--                                                <div class="dropzone-msg dz-message needsclick">--}}
{{--                                                    <h3 class="dropzone-msg-title">Drop images here or click to upload.</h3>--}}
{{--                                                    <span class="dropzone-msg-desc">Upload up to 10 files</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

                                            <input type="file" name="images[]" class="form-control" multiple >

                                            <div id="images"></div>
                                        </div>

                                        <input type="hidden" name="productId" id="product_id" value="">
                                        <div class="mb-3 col-12 text-end pt-5 ">
                                            <button type="submit" class="btn btn-success w-25">Submit</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>




        <div class="modal fade" id="refund-order-modal"  data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered " >
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Refund Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('post.refund.request') }}" method="POST" enctype="multipart/form-data" id="refund-request-form">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                  <div id="update-msj"></div>
                                    <div class="mb-3">
                                        <label class="form-label">Refund Reason</label>
                                        <input type="text" class="form-control" name="refund_reason">
                                 </div>
                                    <div class="mb-3">
                                        <label class="form-label">Refund Note</label>
                                        <textarea class="form-control" name="refund_note" placeholder="Refund Note"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <div class="input-field">
                                            <label class="form-label">Attachments</label>
{{--                                            <div class="dropzone dropzone-default dropzone-primary dz-clickable"--}}
{{--                                                 id="kt_dropzone_2">--}}
{{--                                                <div class="dropzone-msg dz-message needsclick">--}}
{{--                                                    <h3 class="dropzone-msg-title">Drop images here or click to upload.</h3>--}}
{{--                                                    <span class="dropzone-msg-desc">Upload up to 10 files</span>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}

                                            <input type="file" name="images[]" class="form-control" multiple>

                                            <div id="images"></div>
                                        </div>

                                        <div class="mb-3 col-12 text-end pt-5 ">
                                            <button type="submit" class="btn btn-success w-25">Submit</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <!--invoice section end-->
@endsection

@section('javascript')

<script>
    let ajaxConfig = {
        ajaxRequester: function (config, uploadFile, pCall, sCall, eCall) {
            let progress = 0
            let interval = setInterval(() => {
                progress += 10;
                pCall(progress)
                if (progress >= 100) {
                    clearInterval(interval)
                    const windowURL = window.URL || window.webkitURL;
                    sCall({
                        data: windowURL.createObjectURL(uploadFile.file)
                    })
                }
            }, 300)
        }
    }
    $("#demo1").uploader({multiple: true, ajaxConfig: ajaxConfig,autoUpload: false})
</script>

    <script>
        var modal = $("#staticBackdrop");
        var imagesArray = '';

        var uploadImage = [];
        var serverFilenames = [];
        $('.writeReview').on('click', function() {
            var data = $(this).data();
            console.log(data);
            console.log(data.productId);
            $('#product_id').val(data.productId);
            var product_id = data.productId;
            $.ajax({
                url: "{{ route('get.product.review') }}",
                type: "GET",
                data: { product_id: product_id },
                success: function(response) {
                    console.log(response);
                    if (response.status == 'success'){
                        $("#staticBackdrop").modal('show');
                        let rating = modal.find('[name=rating]').val();
                        if (response.review){
                            $('#update-alert').show();

                            let responseRating = response.review.rating;
                            modal.find('[name=rating]').each(function() {
                                if ($(this).val() == responseRating) {
                                    $(this).prop('checked', true);
                                }
                            });
                            modal.find('[name=comment]').val(response.review.comment);
                            imagesArray = response.review.review_image;
                            let imagesHTML = '';

                            for (let i = 0; i < imagesArray.length; i++) {
                                imagesHTML += `<img src="${imagesArray[i].images}" height="150px" width="150px" />`;
                            }
                            $('#images').html(imagesHTML);
                        }else{
                            $('#update-alert').hide();
                            modal.find('[name=rating]').each(function() {
                                if ($(this).val() <= 5) {
                                    $(this).prop('checked', false);
                                }
                            });
                            modal.find('[name=comment]').val('');
                            $('#images').html('');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        });

        var dropzone = $('#kt_dropzone_2').dropzone({
            url: "{{ url('/uploader') }}",
            paramName: "file",
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            accept: function(file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },

        init: function() {
                var dz = this;
            //
            // var dz = this;
            // var mockFile = { name: "http://127.0.0.1:8000/uploads/7RPX1ag6uvhRlEiYBYTwAGAhJKxmMGlQdnu6pMXi.png", size: 12345, type: 'image/jpeg' };
            //
            // this.on("addedfile", function(file) {
            //     formData.append("file", file);
            // });
            // console.log("heloe"+imagesArray);
            // if (imagesArray) {
            //     for (let i = 0; i < imagesArray.length; i++) {
            //         console.log(imagesArray[i]);
            //         fetchImage(imagesArray[i].images, dz);
            //     }
            // }

            console.log("helo wolrd")
            fetch('http://127.0.0.1:8000/uploads/7RPX1ag6uvhRlEiYBYTwAGAhJKxmMGlQdnu6pMXi.png')
                .then(response => response.blob())
                .then(blob => {
                    // Create a File object from the Blob
                    var file = new File([blob], 'example.jpg', { type: 'image/jpeg' });

                    // Add the file to Dropzone manually
                    dz.addFile(file);
                })
                .catch(error => {
                    console.error('Error fetching the image:', error);
                });


            this.on("removedfile", function(file) {
                    // Remove the file from the uploadImage array
                    var index = uploadImage.indexOf(file.name);
                    if (index !== -1) {
                        uploadImage.splice(index, 1);
                    }
                    // Make an AJAX request to delete the file from the server
                    console.log(file.name);
                    $.ajax({
                        url: "{{ url('/delete-image') }}",
                        type: "POST",
                        data: { filename: file.name },
                        success: function(response) {
                            console.log("File removed from server: " + file.name);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error deleting file: " + error);
                        }
                    });
                });
            },
            success: function(file, response) {
                console.log(response);
                serverFilenames.push(file.name)
                uploadImage.push(response);
                console.log(uploadImage);
            }
        });






        $('#product-review').submit(function(event) {
            event.preventDefault();
            // console.log("helo world");
            var formData = new FormData(this);
            for (var i = 0; i < uploadImage.length; i++) {
                formData.append('product_images[]', uploadImage[i]);
            }
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log(data);
                    // $('#kt_modal_add_user').hide();
                    // $('#exampleModal').hide();
                    if (data.status == "success"){
                        $('#staticBackdrop').modal('hide');
                        notifyMe("success", "Review Submitted Successfully .... ")
                    }
                    window.location.reload()
                },
                error: function(data) {
                    console.log(data);
                    var error= data.responseJSON;
                    notifyMe("error", "Rating Field is Required....")
                }
            });
        });


        function fetchImage(imageUrl, dropzoneInstance) {
            fetch(imageUrl)
                .then(response => response.blob())
                .then(blob => {
                    // Create a File object from the Blob
                    const file = new File([blob], 'example.jpg', { type: 'image/jpeg' });

                    // Add the file to Dropzone manually
                    dropzoneInstance.addFile(file);
                })
                .catch(error => {
                    console.error('Error fetching the image:', error);
                });
        }


        $('.refund-order').on('click', function() {
            $("#refund-order-modal").modal('show');
            var data = $(this).data();
            console.log(data);
            var item_qty = data.productQty;
            var item_id = data.itemId;
            console.log(item_qty);
            $('#refund-request-form').submit(function(event) {
                event.preventDefault();
                // console.log("helo world");
                var formData = new FormData(this);
                var product_id = "{{ $product->id }}";
                var order_id = "{{ $order->id }}";

                formData.append('product_id',product_id);
                formData.append('order_id',order_id);

                formData.append('item_qty',item_qty);
                formData.append('item_id',item_id);
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        console.log(data);
                        // $('#kt_modal_add_user').hide();
                        // $('#exampleModal').hide();
                        if (data.status == "success"){
                            $('#refund-order-modal').modal('hide');
                            notifyMe("success", data.message)
                            window.location.reload()
                        }
                    },
                    error: function(data) {
                        console.log(data);
                        var error= data.responseJSON;
                        notifyMe("error", "Rating Field is Required....")
                    }
                });
            });

        });



    </script>
@endsection

@section('script')


   <script>
        (function(){
            var rating = document.querySelector('.rating');
            var handle = document.getElementById('toggle-rating');
            handle.onchange = function(event) {
                rating.classList.toggle('rating', handle.checked);
            };
        }());


    </script>


@endsection
