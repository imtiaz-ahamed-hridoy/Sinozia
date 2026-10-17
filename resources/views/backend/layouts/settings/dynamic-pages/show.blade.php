@extends('backend.app')
@section('title', 'Dynamic Page Details')
@push('style')
<style>
    .show-hide {
        position: absolute;
        right: 20px;
        top: 19px;
        transform: translateY(-50%);
    }

</style>
@endpush
@section('page-content')

<x-breadcrumbs title="Dynamic Pages" subtitle="Dynamic Page Details" />



<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="'Dynamic Page Details'" :subtitle="''" :style="'background-color: #2b5e5e1a;'" />
                <div class="card-body p-4">
                    <div class="content-wrapper">
                        <!-- Banner Image -->
                        <div class="mb-4">
                            @isset($page->banner)
                            <img src="{{ $page->banner ? asset($page->banner) : '' }}" alt="{{ $page->page_title }}" class="img-fluid rounded shadow-sm" style="max-height: 400px; width: 100%; object-fit: cover; object-position: center;">
                            @endisset
                        </div>

                        <!-- Title -->
                        <h2 class="mb-4 fw-bold border-bottom pb-3">{{ $page->page_title }}</h2>

                        <!-- Content -->
                        <div class="page-content">
                            {!! $page->page_content !!}
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Action Buttons -->
                    <div class="text-end mt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('dynamic_page.index') }}" class="btn btn-secondary" title="Cancel and go back to the dashboard">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('dynamic_page.edit', $page->id) }}" class="btn btn-primary" title="Edit this FAQ">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
<!-- Container-fluid Ends-->
@endsection
