@extends('backend.layouts.master')

@section('title')
    {{ localize('Sales Amount Report') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Amount Wise Sales Report') }}</h2>
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
                                    <div class="col-auto">
                                        <div class="input-group">
                                            @php
                                                $start_date = date('m/d/Y', strtotime('7 days ago'));
                                                $end_date = date('m/d/Y', strtotime('today'));
                                                if (isset($date_var)) {
                                                    $start_date = date('m/d/Y', strtotime($date_var[0]));
                                                    $end_date = date('m/d/Y', strtotime($date_var[1]));
                                                }
                                            @endphp

                                            <input class="form-control date-range-picker date-range" type="text"
                                                placeholder="{{ localize('Start date - End date') }}" name="date_range"
                                                data-startdate="'{{ $start_date }}'" data-enddate="'{{ $end_date }}'">
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select select2" name="order"
                                                data-minimum-results-for-search="Infinity">
                                                <option value="DESC"
                                                    @isset($order) @if ($order == 'DESC') selected @endif @endisset>
                                                    {{ localize('High ⟶ Low') }}
                                                </option>

                                                <option value="ASC"
                                                    @isset($order) @if ($order == 'ASC') selected @endif @endisset>
                                                    {{ localize('Low ⟶ High') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>


                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-secondary">
                                            <i data-feather="search" width="18"></i>
                                            {{ localize('Search') }}
                                        </button>
                                    </div>

                                    <div class="col-auto">
                                        <a class="btn btn-primary" href="{{ route('admin.amount.report') }}" role="button">

                                            {{ localize('Download Report') }}
                                        </a>
                                    </div>

                                    <div class="col-auto flex-grow-1"></div>
                                    <div class="col-auto">
                                        <span class="fs-sm">
                                            {{ localize('Total Amount') }}
                                        </span>
                                        <div class="fw-bold text-accent">
                                            {{ formatPrice($totalPrice) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                        <table class="table tt-footable border-top" data-use-parent-width="true">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ localize('S/L') }}</th>
                                    <th>{{ localize('Date') }}</th>
                                    <th class="text-end">{{ localize('Total Sales') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $key => $orderItem)
                                    <tr>
                                        <td class="text-center">
                                            {{ $key + 1 + ($orderItems->currentPage() - 1) * $orderItems->perPage() }}
                                        </td>
                                        <td>
                                            <span
                                                class="fs-sm">{{ date('d M, Y', strtotime($orderItem->created_at)) }}</span>
                                        </td>

                                        <td class="text-end">
                                            {{ formatPrice($orderItem->total_price) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--pagination start-->
                        <div class="d-flex align-items-center justify-content-between px-4 pb-4">
                            <span>{{ localize('Showing') }}
                                {{ $orderItems->firstItem() }}-{{ $orderItems->lastItem() }} {{ localize('of') }}
                                {{ $orderItems->total() }} {{ localize('results') }}</span>
                            <nav>
                                {{ $orderItems->appends(request()->input())->links() }}
                            </nav>
                        </div>
                        <!--pagination end-->
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
