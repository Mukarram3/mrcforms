@extends('backend.layouts.master')

@section('title')
    {{ localize('Products') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection
@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Upload Product Images') }}</h2>
                            </div>
                            <div class="tt-action">

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-6">
                    <div class="card mb-4" id="section-1">
                        <form  action="{{ route('admin.products.index.store.image') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header border-bottom-0">
                                <div class="row justify-content-between g-3">
                                    <div class="col-auto flex-grow-1">
                                        <div class="tt-search-box">
                                            <div class="input-group">
                                                <input class="form-control rounded-start w-100" type="file"
                                                       name="file[]" multiple>
                                            </div>
                                        </div>
                                        <div class="pt-2">

                                            <button class="btn btn-primary " type="submit">
                                                <i data-feather="save" class="me-1"></i> {{ localize('Upload Images') }}
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection