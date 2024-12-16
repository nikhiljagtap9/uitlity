@extends('layout.app')
@section('head')
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0">
                Batches List
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-12">
                    <div class="row brand-white-bg">
                        @if (session('success'))
                            <div class="alert alert-success">
                                <p> {{ session('success') }}</p>
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-error">
                                <p> {{ session('error') }}</p>
                            </div>
                        @endif
                    </div>
                <!--    <table id="example3" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Batch No</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($batches as $batch)
                                <tr>
                                    <td>
                                        <a href="{{ route('disbursebatch.show', [$batch]) }}">
                                            {{ $batch->uuid }}
                                        </a>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($batch->created_at)->format('d-m-Y') }}</td>
                                    <td>{{ $batch->status }}</td>
                                    <td>
                                        <a href="{{ route('disbursebatch.show', [$batch]) }}"
                                            class="btn btn-primary">View</a>
                                        @if ($batch->status != 'Disbursed')
                                            <form id="deleteForm" action="{{ route('disbursebatch.destroy', [$batch]) }}"
                                                method="POST" class="form-group d-inline">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                            </form>
                                            <button class="btn btn-danger " data-toggle="modal"
                                                data-target="#confirmationModal" data-backdrop = "false">Delete</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table> -->

                    <!--begin::Container Display table-->
                    <div id="kt_content_container" class="">
                        <!--begin::Products-->
                        <div class="card card-flush">
                            <!--begin::Card header-->
                            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <!--begin::Search-->
                                    <div class="d-flex align-items-center position-relative my-1">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor" />
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <input type="text" data-kt-ecommerce-product-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Search Product" />
                                    </div>
                                    <!--end::Search-->
                                </div>
                                <!--end::Card title-->
                                <!--begin::Card toolbar-->
                                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                                    <div class="w-100 mw-150px">
                                        <!--begin::Select2-->
                                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true" data-placeholder="Status" data-kt-ecommerce-product-filter="status">
                                            <option></option>
                                            <option value="all">All</option>
                                            <option value="approved">Approved</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        <!--end::Select2-->
                                    </div>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->
                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table-->
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                                    <!--begin::Table head-->
                                    <thead>
                                        <!--begin::Table row-->
                                        <tr class="text-start text-gray-400 fw-bolder fs-7 gs-0">
                                            <th class="d-none"></th>
                                            <th class="d-none"></th>
                                            <th class="min-w-100px">Batch No</th>
                                            <th class="min-w-70px">Date</th>
                                            <th class="min-w-100px d-none"></th>
                                            <th class="min-w-100px d-none"></th>
                                            <th class="min-w-100px">Status</th>
                                            <th class="min-w-70px">Actions</th>
                                        </tr>
                                        <!--end::Table row-->
                                    </thead>
                                    <!--end::Table head-->
                                    <!--begin::Table body-->
                                    <tbody class="fw-bold text-gray-600">
                                        <!--begin::Table row-->
                                        @foreach ($batches as $batch)
                                        <tr>
                                            <td class="d-none"></td>
                                            <td class="d-none"></td>
                                            <td class="pe-0">
                                                <span class="fw-bolder"><a href="{{ route('disbursebatch.show', [$batch]) }}">
                                                    {{ $batch->uuid }}
                                                </a></span>
                                            </td>
                                            <td class="pe-0" data-order="25">
                                                <span class="fw-bolder ms-3">{{ \Carbon\Carbon::parse($batch->created_at)->format('d-m-Y') }}</span>
                                            </td>
                                            <td class="d-none"></td>
                                            <td class="d-none"></td>
                                            <td class="pe-0" data-order="Pending">
                                                <div class="badge badge-light-success">{{ $batch->status }}</div>
                                            </td>
                                            <td>
                                                <a href="{{ route('disbursebatch.show', [$batch]) }}"
                                                    class="btn btn-primary">View</a>
                                                @if ($batch->status != 'Disbursed')
                                                    <form id="deleteForm" action="{{ route('disbursebatch.destroy', [$batch]) }}"
                                                        method="POST" class="form-group d-inline">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="_method" value="DELETE">
                                                    </form>
                                                    <button class="btn btn-danger " data-toggle="modal"
                                                        data-target="#confirmationModal" data-backdrop = "false">Delete</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <!--end::Table body-->
                                </table>
                                <!--end::Table-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <!--end::Products-->
                    </div>
                    <!--end::Container-->
                     {{ $batches->links() }}
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert text-danger" role="alert">
                        <strong>Are you sure you want to delete this batch?</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmSubmit">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to handle form submission when "Confirm" is clicked
        document.getElementById('confirmSubmit').addEventListener('click', function() {
            // Submit the form
            document.getElementById('deleteForm').submit();
        });
    </script>
@endsection
@section('script')
@endsection
