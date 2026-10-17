@extends('backend.app')
@section('title', 'View Target Voice')

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

    <x-breadcrumbs title="Target Voices" subtitle="View Target Voice" />

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <x-table-header title="View Target Voice" subtitle="" />
                    <div class="card-body">
                        <div class="card-wrapper border rounded-3">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Title:</strong></label>
                                    <p>{{ $targetVoice->title }}</p>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label"><strong>Target Voice Audio:</strong></label>
                                    @if ($targetVoice->audio)
                                        <div class="mt-3">
                                            <audio width="300" controls>
                                                <source src="{{ asset('storage/' . $targetVoice->audio) }}"
                                                    type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                    @else
                                        <p>No audio uploaded</p>
                                    @endif
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label"><strong>Status:</strong></label>
                                    <p>
                                        @if ($targetVoice->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('admin.target-voices.index') }}" class="btn btn-secondary"><i
                                            class="fa fa-arrow-left"></i> Back</a>
                                    <a href="{{ route('admin.target-voices.edit', $targetVoice->id) }}"
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
