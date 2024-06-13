@extends('backend.layouts.master')

@section('title')
    {{ localize('Add New Branch') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection


@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Add Branch') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-4">

                <!--left sidebar-->
                <div class="col-xl-9 order-2 order-md-2 order-lg-2 order-xl-1">
                    <form action="{{ route('admin.branch.store') }}" method="POST" class="pb-650">
                        @csrf
                        <!--basic information start-->
                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Basic Information') }}</h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Name') }}</label>
                                    <input class="form-control" type="text" id="name"
                                        placeholder="{{ localize('Type Branch name') }}" name="name" required>
                                </div>
                            </div>
                        </div>



                        <div class="card mb-4" id="section-1">
                            <div class="card-body">
                                <h5 class="mb-3">{{ localize('Banch Phone Number') }}</h5>

                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ localize('Banch Phone Number') }}</label>
                                    <input class="form-control" type="text" id="name"
                                           placeholder="{{ localize('Banch Phone Number') }}" name="phone" required>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4" id="section-3">
                            <div class="card-body">
                                <h5 class="mb-4">{{ localize('Cities') }}</h5>
                                <div class="mb-4">
                                    <select class="select2 select-all form-control" multiple="multiple"
                                            data-placeholder="{{ localize('Select Cities') }}" name="city_name[]"
                                            required>
                                        @foreach ($cities as $city)
                                            @if(\App\Models\BranchCity::where('city_id',$city->id)->first())
                                            @else
                                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                                            @endif
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
                                        <i data-feather="save" class="me-1"></i> {{ localize('Save Branch') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- submit button end -->
                    </form>
                </div>

                <!--right sidebar-->
                <div class="col-xl-3 order-1 order-md-1 order-lg-1 order-xl-2">
                    <div class="card tt-sticky-sidebar d-none d-xl-block">
                        <div class="card-body">
                            <h5 class="mb-3">{{ localize('Branch Information') }}</h5>
                            <div class="tt-vertical-step">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="#section-1" class="active">{{ localize('Basic Information') }}</a>
                                    </li>
                                    <li>
                                        <a href="#section-2">{{ localize('Banner Image') }}</a>
                                    </li>
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
        $(document).on('click', '.select-all', function (e) {
            selectAllSelect2($(this).siblings('.selection').find('.select2-search__field'));
        });

        $(document).on("keyup", ".select2-search__field", function (e) {
            var eventObj = window.event ? event : e;
            if (eventObj.keyCode === 65 && eventObj.ctrlKey)
                selectAllSelect2($(this));
        });


        function selectAllSelect2(that) {

            var selectAll = true;
            var existUnselected = false;
            var item = $(that.parents("span[class*='select2-container']").siblings('select[multiple]'));

            item.find("option").each(function (k, v) {
                if (!$(v).prop('selected')) {
                    existUnselected = true;
                    return false;
                }
            });

            selectAll = existUnselected ? selectAll : !selectAll;

            item.find("option").prop('selected', selectAll);
            item.trigger('change');
        }
    </script>
@endsection
