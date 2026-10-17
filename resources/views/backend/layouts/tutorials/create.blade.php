@extends('backend.app')
@section('title', isset($tutorial) ? 'Edit Tutorial' : 'Create New Tutorial')

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

<x-breadcrumbs title="Tutorials" subtitle="{{ isset($tutorial) ? 'Edit Tutorial' : 'Create New Tutorial' }}" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="isset($tutorial) ? 'Edit Tutorial' : 'Create New Tutorial'" subtitle="" />
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" action="{{ isset($tutorial) ? route('admin.tutorials.update', $tutorial->id) : route('admin.tutorials.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($tutorial))
                            @method('PATCH')
                            @endif
                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Title</label>
                                <input class="form-control  @error('title') is-invalid @enderror" id="inputEmail4" type="text" placeholder="Enter Tutorial Title" value="{{ isset($tutorial) ? $tutorial->title : '' }}" name="title">
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="summernote">Description</label>

                                <textarea class="form-control  @error('description') is-invalid @enderror" type="text" placeholder="Enter Tutorial Description" name="description" rows="4">{{ isset($tutorial) ? $tutorial->description : '' }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Tutorial Image</label>
                                <input class="form-control dropify @error('image_url') is-invalid @enderror" id="inputPassword4" type="file" data-default-file="{{ isset($tutorial) && $tutorial->image_url ? asset($tutorial->image_url) : asset('backend/assets/images/image_placeholder.png') }}" name="image_url">
                                <p class="form-text">Allowed file types: jpg, jpeg, png. Maximum file size: 50 MB.</p>

                                @error('image_url')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <a href="{{ route('admin.tutorials.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ isset($tutorial) ? 'Update' : 'Create' }}</button>
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
