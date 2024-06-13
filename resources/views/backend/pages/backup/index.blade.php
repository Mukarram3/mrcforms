@extends('backend.layouts.master')

@section('title')
    {{ localize('All Database BackUp Section') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection


@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('BackUp Section') }}</h2>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row  g-4">

                <!--left sidebar-->
                <div class="col-xl-6 order-2 order-md-2 order-lg-2 order-xl-2">
                    <a href="{{route('backup.download')}}" class="btn btn-success w-100 my-2">download Backup</a>
                </div>

            </div>

        </div>
    </section>
@endsection
