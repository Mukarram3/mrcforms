@extends('backend.layouts.master')

@section('title')
    {{ localize('Product Price & Stock') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection


@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Product Price & Stock') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row  g-4">

                <!--left sidebar-->
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.product.import') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Import Data') }}</h5>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Name') }}</label>
                                    <select class="select2 form-control" name="branch_id" required>
                                        <option value="">{{ localize('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="card mb-4" id="section-3">
                                <div class="card-body">
                                    <h5 class="mb-4">{{ localize('Select Excel File') }}</h5>
                                    <div class="mb-4">
                                        <input type="file" class="form-control" name="excel">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--basic information end-->


                        <!-- image end-->

                        <!-- submit button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Import Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- submit button end -->
                    </form>
                </div>

                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.product.export') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Export Data') }}</h5>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Name') }}</label>
                                    <select class="select2 form-control" name="branch_id" required>
                                        <option value="">{{ localize('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <!--basic information end-->


                        <!-- image end-->

                        <!-- submit button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Export Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- submit button end -->
                    </form>
                </div>

            </div>
            <div class="row g-4">
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.product.excel.import') }}" enctype="multipart/form-data" method="POST" >
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Product Import Data') }}</h5>
                            </div>
                            <div class="card mb-4" id="section-3">
                                <div class="card-body">
                                    <h5 class="mb-4">{{ localize('Select Excel File') }}</h5>
                                    <div class="mb-4">
                                        <input type="file" class="form-control" name="excel">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--basic information end-->


                        <!-- image end-->

                        <!-- submit button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Import Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- submit button end -->
                    </form>
                </div>



                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.branch.order.export') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Export Order Branch Wise') }}</h5>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Name') }}</label>
                                    <select class="select2 form-control" name="branch_id" required>
                                        <option value="">{{ localize('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <!--basic information end-->


                        <!-- image end-->

                        <!-- submit button -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Export Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- submit button end -->
                    </form>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.branch.product.export') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Export Product Branch Wise') }}</h5>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Name') }}</label>
                                    <select class="select2 form-control" name="branch_id" required>
                                        <option value="">{{ localize('Select Branch') }}</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">
                                                {{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Export Excel') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
{{--Customer json file--}}
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <form action="{{ route('admin.customer.import') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Import Customer Data') }}</h5>
                                <div class="mb-3">
                                    <h5 class="mb-3">{{ localize('Select Excel File') }}</h5>
                                    <input type="file" class="form-control" name="json_file">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Import Json') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

{{--saad--}}
            <div class="row g-4">
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">

                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Export All  Product') }}</h5>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <a href="{{ url('/product-export') }}" role="button" class="btn btn-primary" type="submit">
                                        <i data-feather="save" class="me-1"></i> {{ localize('Export Excel') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>
    </section>
@endsection
