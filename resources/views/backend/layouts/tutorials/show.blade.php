@extends('backend.app')
@section('title', 'Tutorials Details')
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

<x-breadcrumbs title="Tutorials" subtitle="Tutorials Details" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header title="Tutorials Details" subtitle="Details of the selected tutorial" />
                <div class="card-body">
                    <div class="blog-single">
                        <div class="blog-box blog-details">
                            @isset($tutorial->image_url)
                            <img src="{{ asset($tutorial->image_url) }}" alt="{{ $tutorial->title }}" class="img-200 img-fluid" style="max-height: 300px; object-fit: cover; border-radius: 4px;">
                            @endisset

                            <div class="blog-details">
                                <h4 class="mt-3 f-w-600">
                                    {{ $tutorial->title }}
                                </h4>
                                <div class="single-blog-content-top">
                                    <p>{{ $tutorial->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Actions -->
                    <div class="text-end mt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.tutorials.index') }}" class="btn btn-secondary" title="Cancel and go back to the dashboard">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('admin.tutorials.edit', $tutorial->id) }}" class="btn btn-primary" title="Edit this tutorial">
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
