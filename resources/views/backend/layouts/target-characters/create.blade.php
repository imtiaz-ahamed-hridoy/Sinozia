@extends('backend.app')
@section('title', isset($targetCharacter) ? 'Edit Target Character' : 'Create New Target Character')

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

<x-breadcrumbs title="Target Characters" subtitle="{{ isset($targetCharacter) ? 'Edit Target Character' : 'Create New Target Character' }}" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="isset($targetCharacter) ? 'Edit Target Character' : 'Create New Target Character'" subtitle="" />
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" action="{{ isset($targetCharacter) ? route('admin.target-characters.update', $targetCharacter->id) : route('admin.target-characters.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($targetCharacter))
                            @method('PATCH')
                            @endif
                            <div class="col-md-12">
                                <label class="form-label" for="title">Title</label>
                                <input class="form-control  @error('title') is-invalid @enderror" id="title" type="text" placeholder="Enter Target Character Title" value="{{ isset($targetCharacter) ? $targetCharacter->title : '' }}" name="title">
                                @error('title')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="description">Description</label>

                                <textarea class="form-control  @error('description') is-invalid @enderror" id="description" type="text" placeholder="Enter Target Character Description" name="description" rows="4">{{ isset($targetCharacter) ? $targetCharacter->description : '' }}</textarea>
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label" for="image">Target Character Image</label>
                                <input class="form-control  @error('image') is-invalid @enderror" id="image" type="file" placeholder="Upload Target Character Image" name="image">
                                <p class="form-text">Allowed file types: jpeg, png, jpg, gif. Maximum file size: 10 MB.</p>
                                @isset($targetCharacter->image)
                                <div class="mt-3">
                                    <img src="{{ asset($targetCharacter->image) }}" alt="Target Character Image" style="max-width: 300px; max-height: 220px; object-fit: cover;" />
                                </div>
                                @endisset
                                @error('image')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-12">
                                <a href="{{ route('admin.target-characters.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ isset($targetCharacter) ? 'Update' : 'Create' }}</button>
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
