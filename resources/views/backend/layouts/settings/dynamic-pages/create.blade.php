@extends('backend.app')
@section('title', isset($page) ? 'Edit Dynamic Page' : 'Create New Dynamic Page')
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

<x-breadcrumbs title="Dynamic Pages" subtitle="{{ isset($page) ? 'Edit Dynamic Page' : 'Create New Dynamic Page' }}" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="isset($page) ? 'Edit Dynamic Page' : 'Create New Dynamic Page'" subtitle="" />
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" action="{{ isset($page) ? route('dynamic_page.update', $page->id) : route('dynamic_page.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($page))
                            @method('PATCH')
                            @endif
                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Title</label>
                                <input class="form-control  @error('page_title') is-invalid @enderror" id="inputEmail4" type="text" placeholder="Enter Your Title" value="{{ isset($page) ? $page->page_title : '' }}" name="page_title">
                                @error('page_title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            {{-- <div class="col-md-12">
                                <label class="form-label" for="inputPassword4">Banner</label>
                                <input class="form-control dropify @error('banner') is-invalid @enderror" id="inputPassword4" type="file" data-default-file="{{ isset($page) && $page->banner ? asset($page->banner) : asset('backend/assets/images/image_placeholder.png') }}" name="banner">


                            @error('banner')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                            </div> --}}
                    <div class="col-md-12">
                        <label class="form-label" for="summernote">Page Content</label>

                        <textarea class="form-control  @error('page_content') is-invalid @enderror" id="summernote" type="text" placeholder="Enter Your Page Content" name="page_content">{{ isset($page) ? $page->page_content : '' }}</textarea>
                        @error('page_content')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <a href="{{ route('dynamic_page.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                        <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ isset($page) ? 'Update' : 'Create' }}</button>
                    </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>
</div>
<!-- Container-fluid Ends-->
@endsection
