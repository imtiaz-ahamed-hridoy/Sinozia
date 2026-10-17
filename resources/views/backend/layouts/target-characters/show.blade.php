@extends('backend.app')
@section('title', 'View Target Character')

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

    <x-breadcrumbs title="Target Characters" subtitle="View Target Character" />

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <x-table-header title="View Target Character" subtitle="" />
                    <div class="card-body">
                        <div class="card-wrapper border rounded-3">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Title:</strong></label>
                                    <p>{{ $targetCharacter->title }}</p>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Description:</strong></label>
                                    <p>{!! nl2br(e($targetCharacter->description)) !!}</p>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label"><strong>Target Character Image:</strong></label>
                                    @if ($targetCharacter->image)
                                        <div class="mt-3">
                                            <img src="{{ asset('storage/' . $targetCharacter->image) }}"
                                                alt="Target Character Image"
                                                style="max-width: 300px; max-height: 220px; object-fit: cover;" />
                                        </div>
                                    @else
                                        <p>No image uploaded</p>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Status:</strong></label>
                                    <p>
                                        @if ($targetCharacter->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('admin.target-characters.index') }}" class="btn btn-secondary"><i
                                            class="fa fa-arrow-left"></i> Back</a>
                                    <a href="{{ route('admin.target-characters.edit', $targetCharacter->id) }}"
                                        class="btn btn-primary"><i class="fa fa-edit"></i> Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    <!-- Container-fluid Ends-->
@endsection
