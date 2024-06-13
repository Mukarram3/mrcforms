@extends('backend.layouts.master')

@section('title')
    {{ localize('Order Details') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Order Details') }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!--left sidebar-->
                <div class="col-xl-9 order-2 order-md-2 order-lg-2 order-xl-1">
                    <div class="card mb-4" id="section-1">
                        <div class="card-header border-bottom-0">

                            <!--order status-->
                            <div class="row justify-content-between align-items-center g-3">
                                <div class="col-auto flex-grow-1">
                                    <h5 class="mb-0">{{ localize('Invoice') }}
                                        <span
                                            class="text-accent">{{ getSetting('order_code_prefix') }}{{ $order->orderGroup->order_code }}
                                        </span>
                                    </h5>
                                    <span class="text-muted">{{ localize('Order Date') }}:
                                        {{ date('d M, Y', strtotime($order->created_at)) }}
                                    </span>

{{--                                    @if ($order->location_id != null)--}}
                                        <div>
                                            <span class="text-muted">
{{--                                                <i class="las la-map-marker"></i>--}}
{{--                                                {{ optional($order->location)->name }}--}}
                                                Branch Name
                                            </span>

                                            <span class="text-muted">
                                                <i class="las la-map-marker"></i> <span id="branch_name321"></span>
                                            </span>

                                            <div class="col-xl-5 col-lg-6">
                                                    <label for="parent_id" class="form-label">{{ localize('Branch') }}</label>
                                                    <select class="form-control select2" id="branch_name"  name="branch_id" style="width:282px"
                                                            data-toggle="select2">
                                                        @foreach(\App\Models\Branch::all() as $branch)
                                                            <option value="{{ $branch->id }}" {{ $order->branch_name == $branch->name ? "selected" : "" }}>{{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>

                                            </div>
{{--                                            window Re-load button--}}
                                            <a href="#" class="btn btn-primary" id="reload" role="button" style="margin-top: 3px;">Submit</a>
                                        </div>
{{--                                    @endif--}}

                                </div>

                                <div class="col-auto col-lg-3">
                                    <div class="input-group">
                                        <select class="form-select select2" name="payment_status"
                                            data-minimum-results-for-search="Infinity" id="update_payment_status"
                                                @if ($order->payment_status == 'paid') disabled @endif>
                                            <option value="" disabled>
                                                {{ localize('Payment Status') }}
                                            </option>
                                            <option value="paid" @if ($order->payment_status == 'paid') selected @endif>
                                                {{ localize('Paid') }}</option>
                                            <option value="unpaid" @if ($order->payment_status == 'unpaid') selected @endif>
                                                {{ localize('Unpaid') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>


                                <div class="col-auto col-lg-3">
                                    <div class="input-group">
                                        <select class="form-select select2" name="delivery_status"
                                            data-minimum-results-for-search="Infinity" id="update_delivery_status"
                                                @if ($order->delivery_status ==  orderDeliveredStatus()) disabled @endif
                                                @if ($order->delivery_status == orderCancelledStatus()) disabled @endif

                                                @if ($order->delivery_status == "refund_returned") disabled @endif
                                                @if ($order->delivery_status == 'refund_returned_rejected') disabled @endif
                                        >
                                            <option value="" disabled>{{ localize('Delivery Status') }}</option>
                                            <option value="order_placed" @if ($order->delivery_status == orderPlacedStatus()) selected @endif>
                                                {{ localize('Order Placed') }}</option>
                                            <option value="pending" @if ($order->delivery_status == orderPendingStatus()) selected @endif>
                                                {{ localize('Pending') }}
                                            <option value="shipped" @if ($order->delivery_status == 'shipped') selected @endif>
                                                {{ localize('Shipped') }}
                                            <option value="delivered" @if ($order->delivery_status == orderDeliveredStatus()) selected @endif>
                                                {{ localize('Delivered') }}
                                            <option value="cancelled" @if ($order->delivery_status == orderCancelledStatus()) selected @endif>
                                                {{ localize('Cancelled') }}
                                            </option>
                                            <option value="order_returned" @if ($order->delivery_status == 'order_returned') selected @endif>
                                                {{ localize('Order Returned') }}
                                            </option>
                                            <option value="order_returned" @if ($order->delivery_status == 'refund_returned') selected @endif>
                                                {{ localize('Returned/Refunds') }}
                                            </option>
                                            <option value="order_returned" @if ($order->delivery_status == 'refund_returned_rejected') selected @endif>
                                                {{ localize('Returned/Refunds Rejected') }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <a href="{{ route('admin.orders.downloadInvoice', $order->id) }}"
                                        class="btn btn-primary">
                                        <i data-feather="download" width="18"></i>
                                        {{ localize('Download Invoice') }}
                                    </a>
                                </div>
                                {{-- Refend--}}

                                    @if($order->refund_status == 'approved')
                                     <div class="col-auto">
                                         <h5 class="mb-0 text-danger">
                                             {{ localize('Partial Returned/Refunded') }}
                                         </h5>
                                     </div>
                                    @endif
                                {{--//refund--}}
                            </div>

                        <!--customer info-->
                        <div class="card-body">
                            <div class="row justify-content-between g-3">
                                <div class=" col-12" id="tracking_number" style="display: none">
                                    <form method="POST" action="{{ route('admin.orders.update_delivery_status') }}" id="update-status">
                                        @csrf
                                        <label >Enter Tracking Number</label>
                                        <input class="form-control" value="" name="tracking_number" />
                                        <div class="py-2 pb-3">
                                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                        </div>
                                    </form>
                                </div>
                                <div class=" col-12" id="canceled_reason" style="display: none">
                                    <form method="POST" action="{{ route('admin.orders.update_delivery_status') }}" id="update-status-cancel">
                                        @csrf
                                        <label >Enter Canceled Reason</label>
                                        <input class="form-control" value="" name="canceled_reason" />
                                        <div class="py-2 pb-3">
                                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                                        </div>
                                    </form>
                                </div>
                                @php
                                    $shippingAddress = $order->orderGroup->shippingAddress;
                                @endphp
                                <div class="col-xl-7 col-lg-6">
                                    <div class="welcome-message">
                                        <h6 class="mb-2">{{ localize('Customer Info') }}</h6>
                                        <p class="mb-0" >{{ localize('Name') }}: <span id="full_name_text">{{ optional($shippingAddress)->full_name }}</span> </p>
                                        <input type="text" placeholder="Customer Name" value="" id="full_name" style="display: none">

                                        <p class="mb-0">{{ localize('Email') }}:{{ optional($order->user)->email }}</p>


                                        <p class="mb-0">{{ localize('Phone') }}: </i><span id="phone_name_text">{{ optional($shippingAddress)->phone }}</span></p>
                                        <input type="text" placeholder="Phone Number" value="" id="phone" style="display: none">
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
                                    <div class="shipping-address d-flex justify-content-md-end">
                                        <div class="border-end pe-2">
                                            <h6 class="mb-2">{{ localize('Shipping Address') }}</h6>
                                            @php
                                                $shippingAddress = $order->orderGroup->shippingAddress;
                                            @endphp

                                                <p class="mb-0">
                                                @if ($order->orderGroup->is_pos_order)
                                                        {{ $order->orderGroup->pos_order_address }}
                                                    @else
                                                        <i class="fa-solid fa-pen-to-square"></i><span id="shipping_name_text">{{ optional($shippingAddress)->address }},</span>
                                                        <i class="fa-solid fa-pen-to-square"></i><span id="city_name_text"></span>
{{--                                                        {{ optional(optional($shippingAddress)->state)->name }},--}}
                                                            {{ optional(optional($shippingAddress)->country)->name }}
                                                </p>
                                            @endif
                                            <textarea id="address" style="display:none" placeholder="write address"></textarea>
                                            <select class="form-control select2" id="city" class="w-100" data-toggle="select2" style="display: none !important;max-width: 200px;">
                                                @foreach(\App\Models\City::all() as $citie)
                                                    <option value="{{ $citie->id }}">{{ $citie->name }}</option>
                                                @endforeach
                                            </select>

                                        </div>
{{--                                        @if (!$order->orderGroup->is_pos_order)--}}
{{--                                            <div class="ms-4">--}}
{{--                                                <h6 class="mb-2">{{ localize('Billing Address') }}</h6>--}}
{{--                                                @php--}}
{{--                                                    $billingAddress = $order->orderGroup->billingAddress;--}}
{{--                                                @endphp--}}
{{--                                                <p class="mb-0">--}}

{{--                                                    {{ optional($billingAddress)->address }},--}}
{{--                                                    {{ optional(optional($billingAddress)->city)->name }},--}}
{{--                                                    {{ optional(optional($billingAddress)->state)->name }},--}}
{{--                                                    {{ optional(optional($billingAddress)->country)->name }}--}}
{{--                                                </p>--}}
{{--                                            </div>--}}
{{--                                        @endif--}}

                                    </div>
                                    <div class="shipping-address d-flex" style="flex-direction: row;flex-wrap: nowrap">
                                        <div class="pe-2 border-end">
                                            <h6 class="mb-2">{{ localize('Tracking Number') }}</h6>
                                            {{ $order->tracking_number }}
                                        </div>
                                        <div class="ps-4 ">
                                            <h6 class="mb-2">{{ localize('Canceled Reason') }}</h6>
                                            {{ $order->canceled_reason }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-5 col-lg-6">
                                    <div class="mb-4">
                                        <label for="parent_id" class="form-label">{{ localize('Select Product') }}</label>
                                        <select class="form-control select2" id="all_products" name="product_id" class="w-100"
                                                data-toggle="select2">
                                            <option value="0">᎗</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>


                        <!--order details-->
                        <table class="table tt-footable border-top product-table" data-use-parent-width="true">
                            <thead>
                                <tr>
                                    <th class="text-center" width="7%">{{ localize('S/L') }}</th>
                                    <th>{{ localize('Products') }}</th>
{{--                                    <th data-breakpoints="xs sm">{{ localize('Unit Price') }}</th>--}}
                                    <th data-breakpoints="xs sm">{{ localize('QTY') }}</th>
                                    <th data-breakpoints="xs sm">{{ localize('Barcode') }}</th>
                                    <th data-breakpoints="xs sm" class="text-end">{{ localize('Total Price') }}</th>
                                    <th data-breakpoints="xs sm" class="text-end">{{ localize('Action') }}</th>
                                </tr>
                            </thead>
                            @php
                            $lengthProduct = sizeof($order->orderItems);
                            @endphp
                            <tbody>

                                  @foreach ($order->orderItems as $key => $item)
                                    @php
                                        $product = $item->product_variation->product;
                                        // get barcode
                                    $barcode = \App\Models\ProductPrice::where('product_id',$product->id)->first()->bar_code;
//                                    $barcode = \Illuminate\Support\Facades\DB::table('product_prices')->where('product_id',$product->id)->first()->bar_code;

                                    @endphp
                                    <tr>
                                        <td class="text-center" id="srno">{{ $key + 1 }}</td>
                                        <td>
{{--                                            <span id="product_id">{{ $product->id }}</span>--}}
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm"> <img
                                                        src="{{ uploadedAsset($product->thumbnail_image) }}"
                                                        alt="{{ $product->name }}"
                                                        class="rounded-circle">
                                                </div>
                                                <div class="ms-2">
                                                    <h6 class="fs-sm mb-0">
                                                        {{ $product->name }}
                                                    </h6>
                                                    <div class="text-muted">
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

                                        <td class="tt-tb-price">
                                            <span class="fw-bold " onclick="unit_price({{ $item->unit_price }},{{ $item->id }})" id="unit_price_{{ $item->id }}" >{{ formatPrice($item->unit_price) }}<i class="fa-solid fa-pen-to-square"></i></span>
                                            <input type="text" placeholder="Unit Price" value="" id="unit_price_input_{{ $item->id }}" style="display: none">
                                        </td>
{{--                                        <td class="fw-bold" onclick="updateQty({{ $item->qty }},{{ $item->id }},{{ $order->orderGroup->id }})" id="qty_text_{{ $item->id }}" >--}}
{{--                                            {{ $item->qty }}<i class="fa-solid fa-pen-to-square"></i>--}}
{{--                                            <input type="text" placeholder="Qty" value="" id="qty_{{ $item->id }}" style="display: none">--}}

{{--                                        </td>--}}
                                        <td class="fw-bold">
                                            {{$barcode }}
                                        </td>

{{--                                        <td class="fw-bold">--}}
{{--                                            <input type="number" class="form-control" id="discount_price" placeholder="Enter Price">--}}
{{--                                        </td>--}}

                                        <td class="tt-tb-price text-end">
                                            <span class="text-accent fw-bold" onclick="updateTotalPrice({{ $item->total_price }},{{ $item->id }})" id="total_price_text_{{ $item->id }}">{{ formatPrice($item->total_price) }}<i class="fa-solid fa-pen-to-square"></i></span>
                                            <input type="text" placeholder="Total Price" value="" id="total_price_{{ $item->id }}" style="display: none">

                                        </td>

                                        <td class="fw-bold text-end">
                                           <a href="#" onclick="deleteProduct({{ $item->id }})"><i class="fa-sharp fa-solid fa-trash"></i></a>
{{--                                            <button class="btn btn-danger" onclick="deleteProduct({{ $item->id }})">--}}
{{--                                                <i class="fa-sharp fa-solid fa-trash"></i> Delete--}}
{{--                                            </button>--}}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>

                        <!--grand total-->
                        <div class="card-body">
                            <div class="card-footer border-top-0 px-4 py-3 rounded">
                                <div class="row g-4">
                                    <div class="col-auto">
                                        <h6 class="mb-1">{{ localize('Payment Method') }}</h6>
                                        <span id="payment_method">{{ ucwords(str_replace('_', ' ', $order->orderGroup->payment_method)) }}<i class="fa-solid fa-pen-to-square"></i></span>
                                        <input type="text" class="form-control" placeholder="Payment Method" value="" id="payment_method_input" style="display: none">
                                    </div>

                                    <div class="col-auto flex-grow-1">
                                        <h6 class="mb-1">{{ localize('Logistic') }}</h6>
                                        <span id="logistic">{{ $order->logistic_name }}<i class="fa-solid fa-pen-to-square"></i></span>
                                        <input type="text" class="form-control" placeholder="Logistic" value="" id="logistic_input" style="display: none">

                                    </div>

                                    <div class="col-auto">
                                        <h6 class="mb-1">{{ localize('Sub Total') }}</h6>
                                        <strong id="sub_total_amount"><span id="sab_total">{{ formatPrice($order->orderGroup->sub_total_amount) }}<i class="fa-solid fa-pen-to-square"></i></span></strong>
                                        <input type="text" class="form-control" placeholder="Sub Total" value="" id="sab_total_input" style="display: none">
                                    </div>
                                    <div class="col-auto ps-lg-5">
                                        <h6 class="mb-1">{{ localize('Shipping Cost') }}</h6>
                                        <strong id="total_shipping_cost"><span id="shipping_total">{{ formatPrice($order->orderGroup->total_shipping_cost) }}<i class="fa-solid fa-pen-to-square"></i></span></strong>
                                        <input type="text" class="form-control" placeholder="Shipping Price" value="" id="shipping_input" style="display: none">
                                    </div>

                                    @if ($order->orderGroup->total_coupon_discount_amount > 0)
                                        <div class="col-auto ps-lg-5">
                                            <h6 class="mb-1">{{ localize('Coupon Discount') }}</h6>
                                            <strong>{{ formatPrice($order->orderGroup->total_coupon_discount_amount) }}</strong>
                                        </div>
                                    @endif

                                    <div class="col-auto text-lg-end ps-lg-5">
                                        <h6 class="mb-1">{{ localize('Grand Total') }}</h6>
                                        <strong class="text-accent" id="total_amount"><span id="grand_total">{{ formatPrice($order->orderGroup->grand_total_amount) }}<i class="fa-solid fa-pen-to-square"></i></span></strong>
                                        <input type="text" class="form-control" placeholder="Shipping Price" value="" id="grand_total_input" style="display: none">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer border-top-2 pt-2  px-4 py-3 rounded" style="background-color: #F9F9FA;">
                                <div class="row g-4">
                                    <div class="col-12 ps-lg-5">
                                        <h5 class="mb-1">{{ localize('Customer Remarks') }}</h5>
                                        <p class="mb-1">{{ $order->orderGroup->remarks_box }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--right sidebar-->

             </div>
                <div class="col-xl-3 order-1 order-md-1 order-lg-1 order-xl-2">
                    <div class="tt-sticky-sidebar">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">{{ localize('Order Logs') }}</h5>
                                <div class="tt-vertical-step">
                                    <ul class="list-unstyled">
                                        @forelse ($order->orderUpdates as $orderUpdate)
                                            <li>
                                                <a class="{{ $loop->first ? 'active' : '' }}">
                                                    {{ $orderUpdate->note }} <br> By
                                                    <span
                                                            class="text-capitalize">{{ optional($orderUpdate->user)->name }}</span>
                                                    at
                                                    {{ date('d M, Y', strtotime($orderUpdate->created_at)) }}.
                                                </a>
                                            </li>
                                        @empty
                                            <li>
                                                <a class="active">{{ localize('No logs found') }}</a>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
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
{{--unit price invoice--}}
{{--        function unit_price(unit_price,id){--}}
{{--            console.log(unit_price,id)--}}

{{--            $("#unit_price_input_"+id).show();--}}
{{--            $('#unit_price_input_'+id).val(unit_price);--}}


{{--            $('#unit_price_input_'+id).on('change',function (){--}}
{{--                var product_id = id--}}
{{--                var unit_price = $(this).val();--}}
{{--                $.post('{{ route('admin.order.product.update') }}', {--}}
{{--                    _token: '{{ @csrf_token() }}',--}}
{{--                    product_id: product_id,--}}
{{--                    unit_price: unit_price,--}}
{{--                }, function(data) {--}}
{{--                    if (data.status == 'success'){--}}
{{--                        //console.log(data)--}}
{{--                        $('#unit_price_input_'+id).hide();--}}
{{--                        $("#unit_price_"+id).show();--}}
{{--                        $("#unit_price_"+id).text(data.data.unit_price);--}}
{{--                    }--}}
{{--                });--}}
{{--            })--}}
{{--        }--}}
{{--        // qty--}}
{{--        function updateQty(qty,id,order_id){--}}
{{--            console.log(unit_price,id,order_id)--}}

{{--            $("#qty_"+id).show();--}}
{{--            $('#qty_'+id).val(qty);--}}


{{--            $('#qty_'+id).on('change',function (){--}}
{{--                var product_id = id--}}
{{--                var qty = $(this).val();--}}
{{--                $.post('{{ route('admin.order.product.update') }}', {--}}
{{--                    _token: '{{ @csrf_token() }}',--}}
{{--                    product_id: product_id,--}}
{{--                    qty: qty,--}}
{{--                    order_group_id: order_id,--}}
{{--                }, function(data) {--}}
{{--                    if (data.status == 'success'){--}}
{{--                        console.log(data)--}}
{{--                        $('#qty_'+id).hide();--}}
{{--                        $("#qty_text"+id).show();--}}
{{--                        $("#qty_text_"+id).text(data.data.qty);--}}
{{--                        $("#total_price_text_"+id).text(data.data.total_price);--}}
{{--                        $("#shipping_total").text(data.shipping_cost);--}}
{{--                        $("#sab_total").text(data.sub_total);--}}
{{--                        $("#grand_total").text(data.grand_total);--}}
{{--                    }--}}
{{--                });--}}
{{--            })--}}
{{--        }--}}
{{--        // total price--}}
{{--        function updateTotalPrice(total_price,id){--}}
{{--            console.log(total_price,id)--}}

{{--            $("#total_price_"+id).show();--}}
{{--            $('#total_price_'+id).val(total_price);--}}


{{--            $('#total_price_'+id).on('change',function (){--}}
{{--                var product_id = id--}}
{{--                var total_price = $(this).val();--}}
{{--                $.post('{{ route('admin.order.product.update') }}', {--}}
{{--                    _token: '{{ @csrf_token() }}',--}}
{{--                    product_id: product_id,--}}
{{--                    total_price: total_price,--}}
{{--                }, function(data) {--}}
{{--                    if (data.status == 'success'){--}}
{{--                        //console.log(data)--}}
{{--                        $('#total_price_'+id).hide();--}}
{{--                        $("#total_price_text_"+id).show();--}}
{{--                        $("#total_price_text_"+id).text(data.data.total_price);--}}
{{--                    }--}}
{{--                });--}}
{{--            })--}}
{{--        }--}}

        $(document).ready(function () {
            var branch_name = "{{ $order->branch_name }}";
            $('#branch_name321').text(branch_name);

            $('#city').select2();
            var city_text = "{{ \App\Models\City::where('id',$shippingAddress->city_id)->first() ? \App\Models\City::where('id',$shippingAddress->city_id)->first()->name : ""  }}";
               $('#city_name_text').text(city_text);
            // Hide Select2 dropdown initially
            $('#city').next('.select2-container').hide();
        });
        // Reload window

        $(document).ready(function(){
            $("#reload").click(function(){
                    location.reload(true);
            });
        });
    </script>
    <script type="text/javascript">
        window.onload = function () {
            getAllProducts();
        };

{{--        $('#full_name_text').on('click',function (){--}}
{{--            var text = $(this).text();--}}
{{--            // console.log('text',text);--}}
{{--            $(this).hide()--}}
{{--            $("#full_name").show();--}}
{{--            $('#full_name').val(text);--}}


{{--            console.log("asdasdasdasdasdd");--}}
{{--        })--}}

{{--        $('#full_name').on('change',function (){--}}
{{--            var address_id = "{{ $shippingAddress ? $shippingAddress->id : '' }}";--}}
{{--            var name = $('#full_name').val();--}}
{{--            $.post('{{ route('admin.update.address') }}', {--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                address_id: address_id,--}}
{{--                name: name,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    $('#full_name').hide();--}}
{{--                    $("#full_name_text").show();--}}
{{--                    $("#full_name_text").text(data.data.full_name);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}

{{--        //Phone Number--}}
{{--        $('#phone_name_text').on('click',function (){--}}
{{--            var phone = $(this).text();--}}
{{--             //console.log('text',phone);--}}
{{--            $(this).hide()--}}
{{--            $("#phone").show();--}}
{{--            $('#phone').val(phone);--}}
{{--        })--}}

{{--        $('#phone').on('change',function (){--}}
{{--            var address_id = {{ $shippingAddress->id }};--}}
{{--            var phone = $('#phone').val();--}}
{{--            $.post('{{ route('admin.update.address') }}', {--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                address_id: address_id,--}}
{{--                phone: phone,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    $('#phone').hide();--}}
{{--                    $("#phone_name_text").show();--}}
{{--                    $("#phone_name_text").text(data.data.phone);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}


{{--        //address--}}
{{--        $('#shipping_name_text').on('click',function (){--}}
{{--            var address = $(this).text();--}}
{{--            //console.log('text',phone);--}}
{{--            $(this).hide()--}}
{{--            $("#address").show();--}}
{{--            $('#address').val(address);--}}
{{--        })--}}

{{--        $('#address').on('change',function (){--}}
{{--            var address_id = {{ $shippingAddress->id }};--}}
{{--            var address = $('#address').val();--}}
{{--            $.post('{{ route('admin.update.address') }}', {--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                address_id: address_id,--}}
{{--                address: address,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    $('#address').hide();--}}
{{--                    $("#shipping_name_text").show();--}}
{{--                    $("#shipping_name_text").text(data.data.address);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}
{{--//city--}}
{{--        $('#city_name_text').on('click',function (){--}}
{{--            var city = $(this).text();--}}
{{--            //console.log('text',city);--}}
{{--            $(this).hide()--}}
{{--            $("#city").show();--}}
{{--            $('#city').val(city);--}}
{{--             $('#city').next('.select2-container').show();--}}
{{--        })--}}


{{--        $('#city').on('change',function (){--}}
{{--            var address_id = {{ $shippingAddress->id }};--}}
{{--            var city = $('#city').val();--}}
{{--            $.post('{{ route('admin.update.address') }}', {--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                address_id: address_id,--}}
{{--                city_id: city,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    //console.log(data)--}}
{{--                    $('#city').next('.select2-container').hide();--}}
{{--                    $("#city_name_text").show();--}}
{{--                    $("#city_name_text").text(data.data.city.name);--}}

{{--                }--}}
{{--            });--}}
{{--        })--}}


{{--        //Payment Method--}}
{{--        $('#payment_method').on('click',function (){--}}
{{--            var payment_method_input = $(this).text();--}}
{{--            // console.log('text',shipping);--}}
{{--            $(this).hide()--}}
{{--            $("#payment_method_input").show();--}}
{{--            $('#payment_method_input').val(payment_method_input);--}}

{{--        })--}}

{{--        $('#payment_method_input').on('change',function (){--}}
{{--            var order_id = {{ $order->id }};--}}
{{--            var payment_method = $('#payment_method_input').val();--}}
{{--            $.post('{{ route('admin.order.paymentmethod.update') }}',{--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                order_id: order_id,--}}
{{--                payment_method: payment_method,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    // console.log(data);--}}
{{--                    $('#payment_method_input').hide();--}}
{{--                    $("#payment_method").show();--}}
{{--                    $("#payment_method").text(data.data.payment_method);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}

{{--        //Logistic--}}
{{--        $('#logistic').on('click',function (){--}}
{{--            var logistic_input = $(this).text();--}}
{{--            // console.log('text',shipping);--}}
{{--            $(this).hide()--}}
{{--            $("#logistic_input").show();--}}
{{--            $('#logistic_input').val(logistic_input);--}}

{{--        })--}}

{{--        $('#logistic_input').on('change',function (){--}}
{{--            var order_id = {{ $order->id }};--}}
{{--            var logistic_name = $('#logistic_input').val();--}}
{{--            $.post('{{ route('admin.order.logistic.update') }}',{--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                order_id: order_id,--}}
{{--                logistic_name: logistic_name,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    // console.log(data);--}}
{{--                    $('#logistic_input').hide();--}}
{{--                    $("#logistic").show();--}}
{{--                    $("#logistic").text(data.data.logistic_name);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}

{{--        //subtotal--}}
{{--        $('#sab_total').on('click',function (){--}}
{{--            var sab_total_input = $(this).text();--}}
{{--            // console.log('text',shipping);--}}
{{--            $(this).hide()--}}
{{--            $("#sab_total_input").show();--}}
{{--            $('#sab_total_input').val(sab_total_input);--}}

{{--        })--}}

{{--        $('#sab_total_input').on('change',function (){--}}
{{--            var order_id = {{ $order->id }};--}}
{{--            var sub_total_amount = $('#sab_total_input').val();--}}
{{--            $.post('{{ route('admin.order.subtotal.update') }}',{--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                order_id: order_id,--}}
{{--                sub_total_amount: sub_total_amount,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    // console.log(data);--}}
{{--                    $('#sab_total_input').hide();--}}
{{--                    $("#sab_total").show();--}}
{{--                    $("#sab_total").text(data.data.sub_total_amount);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}

{{--        //shipping--}}
{{--        $('#shipping_total').on('click',function (){--}}
{{--            var shipping_input = $(this).text();--}}
{{--           // console.log('text',shipping);--}}
{{--            $(this).hide()--}}
{{--            $("#shipping_input").show();--}}
{{--            $('#shipping_input').val(shipping_input);--}}
{{--        })--}}

{{--        $('#shipping_input').on('change',function (){--}}
{{--            var order_id = {{ $order->orderGroup->id }};--}}
{{--            var shipping_cost = $('#shipping_input').val();--}}
{{--            $.post('{{ route('admin.order.shipping.update') }}',{--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                order_id: order_id,--}}
{{--                shipping_cost: shipping_cost,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                   // console.log(data);--}}
{{--                    $('#shipping_input').hide();--}}
{{--                    $("#shipping_total").show();--}}
{{--                    $("#shipping_total").text(data.data.total_shipping_cost);--}}
{{--                    $("#grand_total").text(data.data.grand_total_amount);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}

{{--        //GrandTotal--}}
{{--        $('#grand_total').on('click',function (){--}}
{{--            var grand_total_input = $(this).text();--}}
{{--            // console.log('text',shipping);--}}
{{--            $(this).hide()--}}
{{--            $("#grand_total_input").show();--}}
{{--            $('#grand_total_input').val(grand_total_input);--}}

{{--        })--}}

{{--        $('#grand_total_input').on('change',function (){--}}
{{--            var order_id = {{ $order->orderGroup->id }};--}}
{{--            var grand_total_amount = $('#grand_total_input').val();--}}
{{--            $.post('{{ route('admin.order.grandtotal.update') }}',{--}}
{{--                _token: '{{ @csrf_token() }}',--}}
{{--                order_id: order_id,--}}
{{--                grand_total_amount: grand_total_amount,--}}
{{--            }, function(data) {--}}
{{--                if (data.status == 'success'){--}}
{{--                    // console.log(data);--}}
{{--                    $('#grand_total_input').hide();--}}
{{--                    $("#grand_total").show();--}}
{{--                    $("#grand_total").text(data.data.grand_total_amount);--}}
{{--                }--}}
{{--            });--}}
{{--        })--}}


        // payment status
        $('#update_payment_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_payment_status').val();
            $.post('{{ route('admin.orders.update_payment_status') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                status: status
            }, function(data) {
                notifyMe('success', '{{ localize('Payment status has been updated') }}');
                window.location.reload();
            });
        });

        // delivery status 
        $('#update_delivery_status').on('change', function() {
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            if(status == "shipped"){
                $('#tracking_number').show();
            }else if(status == 'cancelled'){
                $('#canceled_reason').show();
            }else{
                $.post('{{ route('admin.orders.update_delivery_status') }}', {
                    _token: '{{ @csrf_token() }}',
                    order_id: order_id,
                    status: status
                }, function(data) {
                    notifyMe('success', '{{ localize('Delivery status has been updated') }}');
                    window.location.reload();
                });
            }

        });


        $('#update-status').submit(function(event) {
            event.preventDefault();
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            var formData = new FormData(this);
            formData.append('order_id',order_id);
            formData.append('status',status);
            console.log(formData);
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                  console.log(data);
                    notifyMe('success', '{{ localize('Delivery status has been updated') }}');
                    window.location.reload();
                },
                error: function(data) {
                    var error= data.responseJSON;
                    console.log(error);
                }
            });
        });

        $('#update-status-cancel').submit(function(event) {
            event.preventDefault();
            var order_id = {{ $order->id }};
            var status = $('#update_delivery_status').val();
            var formData = new FormData(this);
            formData.append('order_id',order_id);
            formData.append('status',status);
            console.log(formData);
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                  console.log(data);
                    notifyMe('success', '{{ localize('Delivery status has been updated') }}');
                    window.location.reload();
                },
                error: function(data) {
                    var error= data.responseJSON;
                    console.log(error);
                }
            });
        });


        $(document).on('change', '[name=branch_id]', function() {
            var branch_id = $(this).val();
            var order_id = {{ $order->id }};

            var Host_url = "{{ url('/') }}";
            $.ajax({
                url: Host_url+'/admin/getBranchName', // Replace this with the URL pointing to your backend endpoint
                method: 'GET',
                data: {
                    branch_id: branch_id,
                    order_id: order_id,
                },
                success: function(response) {
                    var branch_name = response.branch;
                    console.log(response);
                    $("#branch_name321").text(branch_name);
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                }
            });
            getAllProducts(branch_id);
        });




        function getAllProducts(branch_id) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                url: "{{ route('admin.get.allproduct') }}",
                type: 'POST',
                data: {
                    branch_id: branch_id
                },
                success: function(response) {
                    $('[name="product_id"]').html("");
                    $('[name="product_id"]').html(JSON.parse(response));
                }
            });
        }

        $('#all_products').on('change',function (){
            var order_id = {{ $order->id }};
            var product_id = $('#all_products').val();
            var rowCount = $('.product-table:last tr').length;
            var srno = rowCount;
            $.post('{{ route('admin.update.order') }}', {
                _token: '{{ @csrf_token() }}',
                order_id: order_id,
                product_id: product_id,
                order_code: '{{ $order->orderGroup->order_code }}',
            }, function(data) {
                if (data.status === 'success') {
                    console.log(data);
                    $('#total_shipping_cost').text(data.total_shiping_cost);
                    $('#total_amount').text(data.grand_total);
                    $('#sub_total_amount').text(data.sub_total);
                    $('.product-table').append(`
                                <tr>
                                    <td class="text-center" id="srno">${srno}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm"> <img
                                                       src='${data.product[0].image}' class="rounded-circle">
                                            </div>
                                            <div class="ms-2">
                                                <h6 class="fs-sm mb-0">
                                                    ${data.product[0].name}
                                                </h6>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="tt-tb-price">
                                            <span class="fw-bold">${data.orderItems.unit_price}
                                            </span>
                                    </td>


                                     <td class="tt-tb-price">
                                            <span class="fw-bold">${data.orderItems.qty}
                                            </span>
                                    </td>
                                    <td class="tt-tb-price text-center">
                                            <span class="text-accent fw-bold">RS ${data.orderItems.unit_price}
                                            </span>
                                    </td>

</tr>
            `);
                }
            });
        })


    </script>
{{--Delete Product--}}
    <script>
        var Host_url = "{{ url('/') }}";
        function deleteProduct(itemId) {
            if (confirm("Are you sure you want to delete this product?")) {
                // Make an AJAX request to delete the product
                $.ajax({
                    url: Host_url+'/admin/logistics/delete/invoice/produt/' + itemId,
                    type: 'GET',
                    success: function(response) {
                        // Handle success, e.g., remove the row from the table
                        $('#row_' + itemId).remove();
                        window.location.reload();
                    },
                    error: function(error) {
                        // Handle error
                        console.error('Error deleting product:', error);
                    }
                });
            }
        }
    </script>
@endsection
