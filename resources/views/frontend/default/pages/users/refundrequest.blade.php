@extends('frontend.default.layouts.master')

@section('title')
    {{ localize('Customer Order Refund/Return Request') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="my-account pt-6 pb-120">
        <div class="container">

            @include('frontend.default.pages.users.partials.customerHero')

            <div class="row g-4">
                <div class="col-xl-3">
                    @include('frontend.default.pages.users.partials.customerSidebar')
                </div>

                <div class="col-xl-9">
                    <div class="recent-orders bg-white rounded py-5">
                        <h6 class="mb-4 px-4">{{ localize('Your Orders') }}</h6>
                        <div class="table-responsive">
                            <table class="order-history-table table">
                                <tbody>
                                <tr>
                                    <th>{{ localize('Order Code') }}</th>
                                    <th>{{ localize('Placed on') }}</th>
                                    <th>{{ localize('Quantity') }}</th>
                                    <th>{{ localize('Price') }}</th>
                                    <th>{{ localize('Product') }}</th>
                                    <th>{{ localize('Status') }}</th>
                                </tr>

                                @forelse ($orders as $order)
                                    <tr>
                                        <td>{{ getSetting('order_code_prefix') }}{{ \App\Models\OrderGroup::where('id',$order->order->order_group_id)->first()->order_code }}
                                        </td>
                                        <td>{{ date('d M, Y', strtotime($order->created_at)) }}</td>
                                        <td>{{ $order->refundItems->quantity }}</td>
                                        <td class="text-secondary">
                                            {{ formatPrice($order->amount) }}</td>

                                        <td>
                                            {{ $order->refundItems->orderItem->product_variation->product->name }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucwords(str_replace('_', ' ', $order->admin_approval)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="px-1 px-md-3">
                            {{ $orders->appends(request()->input())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
