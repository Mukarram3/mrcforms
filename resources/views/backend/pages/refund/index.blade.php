@extends('backend.layouts.master')

@section('title')
    {{ localize('Orders') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Orders') }}</h2>
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
                                    <div class="col-auto flex-grow-1 d-none">
                                        <div class="tt-search-box">
                                            <div class="input-group">
                                                <span class="position-absolute top-50 start-0 translate-middle-y ms-2"> <i
                                                            data-feather="search"></i></span>
                                                <input class="form-control rounded-start w-100" type="text"
                                                       id="search" name="search"
                                                       placeholder="{{ localize('Search by name/phone') }}"
                                                       @isset($searchKey)
                                                           value="{{ $searchKey }}"
                                                        @endisset>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto flex-grow-1">
                                        <div class="input-group mb-3">
                                            @if (getSetting('order_code_prefix') != null)
                                                <div class="input-group-prepend">
                                                    <span
                                                            class="input-group-text rounded-end-0">{{ getSetting('order_code_prefix') }}</span>
                                                </div>
                                            @endif
                                            <input type="text" class="form-control" placeholder="{{ localize('code') }}"
                                                   name="code"
                                                   @isset($searchCode)
                                                       value="{{ $searchCode }}"
                                                    @endisset>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <select class="form-select select2" name="payment_status"
                                                data-minimum-results-for-search="Infinity" id="payment_status">
                                            <option value="">{{ localize('Payment Status') }}</option>
                                            <option value="{{ paidPaymentStatus() }}"
                                                    @if (isset($paymentStatus) && $paymentStatus == paidPaymentStatus()) selected @endif>
                                                {{ localize('Paid') }}</option>
                                            <option value="{{ unpaidPaymentStatus() }}"
                                                    @if (isset($paymentStatus) && $paymentStatus == unpaidPaymentStatus()) selected @endif>
                                                {{ localize('Unpaid') }}</option>
                                        </select>
                                    </div>

                                    <div class="col-auto">
                                        <select class="form-select select2" name="delivery_status"
                                                data-minimum-results-for-search="Infinity" id="update_delivery_status">
                                            <option value="">{{ localize('Delivery Status') }}</option>
                                            <option value="order_placed" @if (isset($deliveryStatus) && $deliveryStatus == orderPlacedStatus()) selected @endif>
                                                {{ localize('Order Placed') }}</option>
                                            <option value="pending" @if (isset($deliveryStatus) && $deliveryStatus == orderPendingStatus()) selected @endif>
                                            {{ localize('Pending') }}
                                            <option value="processing" @if (isset($deliveryStatus) && $deliveryStatus == orderProcessingStatus()) selected @endif>
                                            {{ localize('Processing') }}
                                            <option value="delivered" @if (isset($deliveryStatus) && $deliveryStatus == orderDeliveredStatus()) selected @endif>
                                            {{ localize('Delivered') }}
                                            <option value="cancelled" @if (isset($deliveryStatus) && $deliveryStatus == orderCancelledStatus()) selected @endif>
                                                {{ localize('Cancelled') }}
                                            </option>
                                        </select>
                                    </div>

                                    @if (count($locations) > 0)
                                        <div class="col-auto">
                                            <select class="form-select select2" name="location_id"
                                                    data-minimum-results-for-search="Infinity" id="location_id">
                                                <option value="">{{ localize('Location') }}</option>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}"
                                                            @if (isset($locationId) && $locationId == $location->id) selected @endif>
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif

                                    <div class="col-auto">
                                        <select class="form-select select2" name="is_pos_order"
                                                data-minimum-results-for-search="Infinity" id="is_pos_order">
                                            <option value="0" @if (isset($posOrder) && $posOrder == 0) selected @endif>
                                                {{ localize('Online Orders') }}
                                            </option>
                                            <option value="1" @if (isset($posOrder) && $posOrder == 1) selected @endif>
                                                {{ localize('POS Orders') }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">
                                            <i data-feather="search" width="18"></i>
                                            {{ localize('Search') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table tt-footable border-top align-middle" data-use-parent-width="true">
                            <thead>
                            <tr>

                                <th class="text-center">{{ localize('S/L') }}</th>
                                <th>{{ localize('Order Code') }}</th>
                                <th data-breakpoints="xs sm md">{{ localize('Customer') }}</th>
                                <th>{{ localize('Placed On') }}</th>
                                <th data-breakpoints="xs">{{ localize('Items') }}</th>
                                <th data-breakpoints="xs sm">{{ localize('Status') }}</th>
                                {{--                                    @if (count($locations) > 0)--}}
                                <th data-breakpoints="xs sm">{{ localize('Amount') }}</th>
                                <th data-breakpoints="xs sm">{{ localize('Reason') }}</th>
                                {{--                                    @endif--}}
                                <th data-breakpoints="xs sm" class="text-end">{{ localize('Action') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @forelse ($orders as $key=> $order)
                                <tr data-bs-toggle="collapse" data-bs-target="#collapseExample">
                                    <td class="text-center">{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td>{{ getSetting('order_code_prefix') }} {{ $order->order->orderGroup->order_code }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img class="rounded-circle"
                                                     src="{{ uploadedAsset(optional($order->user)->avatar) }}"
                                                     alt="avatar"
                                                     onerror="this.onerror=null;this.src='{{ staticAsset('backend/assets/img/placeholder-thumb.png') }}';" />
                                            </div>
                                            <div class="ms-2">
                                                <h6 class="fs-sm mb-0">{{ optional($order->user)->name }}</h6>
                                                <span class="text-muted fs-sm">
                                                        {{ optional($order->user)->phone ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ date('d M, Y', strtotime($order->created_at)) }}</td>

                                    @if(isset($order->refundItems->orderItem->product_variation->product))
                                        <td>{{ $order->refundItems->orderItem->product_variation->product ? $order->refundItems->orderItem->product_variation->product->name : "Product Deleted" }}
                                        </td>
                                    @else
                                        <td>Product Deleted</td>
                                    @endif

                                    <td>
                                        @if ($order->admin_approval == 'pending')
                                            <span class="badge bg-soft-primary rounded-pill text-capitalize">
                                                    {{ $order->admin_approval }}
                                                </span>
                                        @else
                                            <span class="badge bg-soft-info rounded-pill text-capitalize">
                                                    {{ localize(Str::title(Str::replace('_', ' ', $order->admin_approval))) }}
                                                </span>
                                        @endif
                                    </td>
                                    <td class="text-secondary">
                                        {{ formatPrice($order->amount) }}</td>

                                    <td class="text-center">
                                        {{ $order->reason }} <br>
                                        <?php
                                            $attachments = explode(',', $order->attachments);
                                            ?>


                                        @foreach($attachments as $img)
                                            <a href="{{ staticAsset($img) }}"><img src="{{ staticAsset($img) }}" style="height: 70px;width: 70px"></a>
                                        @endforeach

                                    </td>
                                    <td class="text-end">
                                        <a href="{{url('/admin/refund-status/' . $order->id . '?status=rejected&order_id=' . $order->order->id) }}" class="btn btn-sm @if($order->order->refund_status=="rejected" || $order->order->refund_status=="approved" ) disabled  @endif btn-danger"  onclick="changeStatus('rejected', '{{ $order->id }}','{{ $order->order->id }}')">Rejected</a>
                                        <a href="{{ url('/admin/refund-status/'.$order->id."?status=approved&order_id=".$order->order->id) }}" class="btn btn-sm @if($order->order->refund_status=="approved" || $order->order->refund_status=="rejected") disabled  @endif btn-success"  onclick="changeStatus('approved', '{{ $order->id }}','{{ $order->order->id }}')">Approved</a>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                        <!--pagination start-->
                        <div class="d-flex align-items-center justify-content-between px-4 pb-4">
                            <span>{{ localize('Showing') }}
                                {{ $orders->firstItem() }}-{{ $orders->lastItem() }} {{ localize('of') }}
                                {{ $orders->total() }} {{ localize('results') }}</span>
                            <nav>
                                {{ $orders->appends(request()->input())->links() }}
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
        function changeStatus(status, orderId,m_order_id) {
            var newURL = '/admin/refund-status/' + orderId + '?status=' + status +"&order_id="+ m_order_id;
            // You can perform additional actions here before changing the URL if needed
            window.location.href = newURL; // Redirects to the updated URL
        }
    </script>
@endsection
