@extends('backend.layouts.master')

@section('title')
    {{ localize('Branches') }} {{ getSetting('title_separator') }} {{ getSetting('system_title') }}
@endsection

@section('contents')
    <section class="tt-section pt-4">
        <div class="container">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card tt-page-header">
                        <div class="card-body d-lg-flex align-items-center justify-content-lg-between">
                            <div class="tt-page-title">
                                <h2 class="h5 mb-lg-0">{{ localize('Branches') }}</h2>
                            </div>
                            <div class="tt-action">
{{--                                @can('add_locations')--}}
                                    <a href="{{ route('admin.branch.create') }}" class="btn btn-primary"><i
                                            data-feather="plus"></i> {{ localize('Add Branch') }}</a>
{{--                                @endcan--}}
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
                                    <div class="col-auto flex-grow-1">
                                        <div class="tt-search-box">
                                            <div class="input-group">
                                                <span class="position-absolute top-50 start-0 translate-middle-y ms-2"> <i
                                                        data-feather="search"></i></span>
                                                <input class="form-control rounded-start w-100" type="text"
                                                    id="search" name="search" placeholder="{{ localize('Search') }}"
                                                    @isset($searchKey)
                                                value="{{ $searchKey }}"
                                                @endisset>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="input-group">
                                            <select class="form-select select2" name="is_published"
                                                data-minimum-results-for-search="Infinity">
                                                <option value="">{{ localize('Select Status') }}</option>
                                                <option value="1"
                                                    @isset($is_active)
                                                         @if ($is_active == 1) selected @endif
                                                        @endisset>
                                                    {{ localize('Published') }}</option>
                                                <option value="0"
                                                    @isset($is_active)
                                                         @if ($is_active == 0) selected @endif
                                                        @endisset>
                                                    {{ localize('Hidden') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-secondary">
                                            <i data-feather="search" width="18"></i>
                                            {{ localize('Search') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <table class="table tt-footable border-top" data-use-parent-width="true">
                            <thead>
                                <tr>
                                    <th class="text-center">{{ localize('S/L') }}
                                    </th>
                                    <th>{{ localize('Name') }}</th>
                                    <th>{{ localize('Phone') }}</th>
                                    <th data-breakpoints="xs sm md">{{ localize('Published') }}</th>
                                    <th data-breakpoints="xs sm md" class="text-end">{{ localize('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($branches as $key => $branch)
                                    <tr>
                                        <td class="text-center">
                                            {{ $key + 1 + ($branches->currentPage() - 1) * $branches->perPage() }}</td>
                                        <td>
                                            <a href="javascript:void(0);" class="d-flex align-items-center" target="_blank">
{{--                                                <div class="avatar avatar-sm">--}}
{{--                                                    <img class="rounded-circle"--}}
{{--                                                        src="{{ uploadedAsset($location->banner) }}" alt=""--}}
{{--                                                        onerror="this.onerror=null;this.src='{{ staticAsset('backend/assets/img/placeholder-thumb.png') }}';" />--}}
{{--                                                </div>--}}
                                                <h6 class="fs-sm mb-0 ms-2">{{ $branch->name }}</h6>
                                            </a>
                                        </td>

                                        <td>
                                            <a href="javascript:void(0);" class="d-flex align-items-center" target="_blank">
                                                {{--                                                <div class="avatar avatar-sm">--}}
                                                {{--                                                    <img class="rounded-circle"--}}
                                                {{--                                                        src="{{ uploadedAsset($location->banner) }}" alt=""--}}
                                                {{--                                                        onerror="this.onerror=null;this.src='{{ staticAsset('backend/assets/img/placeholder-thumb.png') }}';" />--}}
                                                {{--                                                </div>--}}
                                                <h6 class="fs-sm mb-0 ms-2">{{ $branch->phone }}</h6>
                                            </a>
                                        </td>
{{--                                        <td>{{ $location->address }}</td>--}}

{{--                                        <td>--}}
{{--                                            @can('publish_locations')--}}
{{--                                                <div class="form-check form-switch">--}}
{{--                                                    <input type="checkbox" onchange="updateDefaultStatus(this)"--}}
{{--                                                        class="form-check-input"--}}
{{--                                                        @if ($location->is_default) checked @endif--}}
{{--                                                        value="{{ $location->id }}">--}}
{{--                                                </div>--}}
{{--                                            @endcan--}}
{{--                                        </td>--}}

                                        <td>
                                            @can('publish_locations')
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" onchange="updatePublishedStatus(this)"
                                                        class="form-check-input"
                                                        @if ($branch->status) checked @endif
                                                        value="{{ $branch->id }}">
                                                </div>
                                            @endcan

                                        </td>
                                        <td class="text-end">
                                            <div class="dropdown tt-tb-dropdown">
                                                <button type="button" class="btn p-0" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i data-feather="more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-end shadow">
                                                    @can('edit_locations')
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.branch.edit', ['id' => $branch->id, 'lang_key' => env('DEFAULT_LANGUAGE')]) }}&localize">
                                                            <i data-feather="edit-3" class="me-2"></i>{{ localize('Edit') }}
                                                        </a>
                                                    @endcan
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!--pagination start-->
                        <div class="d-flex align-items-center justify-content-between px-4 pb-4">
                            <span>{{ localize('Showing') }}
                                {{ $branches->firstItem() }}-{{ $branches->lastItem() }} {{ localize('of') }}
                                {{ $branches->total() }} {{ localize('results') }}</span>
                            <nav>
                                {{ $branches->appends(request()->input())->links() }}
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

        // update default status
        function updateDefaultStatus(el) {
            if (el.checked) {
                var status = 1;
            } else {
                var status = 0;
            }
            $.post('{{ route('admin.locations.updateDefaultStatus') }}', {
                    _token: '{{ csrf_token() }}',
                    id: el.value,
                    status: status
                },
                function(data) {
                    if (data == 1) {
                        notifyMe('success', '{{ localize('Status updated successfully') }}');
                        location.reload()
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
            $.post('{{ route('admin.branch.updatePublishedStatus') }}', {
                    _token: '{{ csrf_token() }}',
                    id: el.value,
                    status: status
                },
                function(data) {
                    if (data == 1) {
                        notifyMe('success', '{{ localize('Status updated successfully') }}');
                    } else if (data == 3) {
                        notifyMe('warning', '{{ localize('Default location can not be hidden') }}');
                    } else {
                        notifyMe('danger', '{{ localize('Something went wrong') }}');
                    }
                });
        }
    </script>
@endsection
