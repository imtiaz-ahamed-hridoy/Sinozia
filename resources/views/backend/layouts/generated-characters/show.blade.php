@extends('backend.app')
@section('title', 'View Generated Character')

@push('style')
    <style>
        .show-hide {
            position: absolute;
            right: 20px;
            top: 19px;
            transform: translateY(-50%);
        }
        .badge-pill {
            padding: 6px 12px;
            border-radius: 50rem;
        }
    </style>
@endpush

@section('page-content')

    <x-breadcrumbs title="Generated Characters" subtitle="View Generated Character" />

    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <x-table-header title="View Generated Character" subtitle="" />
                    <div class="card-body">
                        <div class="card-wrapper border rounded-3">
                            <div class="row g-3">

                                {{-- User --}}
                                <div class="col-md-6">
                                    <label class="form-label"><strong>User:</strong></label>
                                    <p>{{ $generatedCharacter->user?->name ?? 'N/A' }}</p>
                                </div>

                                {{-- Title --}}
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Title:</strong></label>
                                    <p>{{ $generatedCharacter->title ?? '—' }}</p>
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Status:</strong></label>
                                    <p>
                                        @php
                                            $statusColors = [
                                                'pending'    => 'secondary',
                                                'generating' => 'warning',
                                                'completed'  => 'success',
                                                'failed'     => 'danger',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$generatedCharacter->status] ?? 'secondary' }} badge-pill">
                                            {{ ucfirst($generatedCharacter->status) }}
                                        </span>
                                    </p>
                                </div>

                                {{-- Active Status --}}
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Active:</strong></label>
                                    <p>
                                        @if ($generatedCharacter->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </p>
                                </div>

                                {{-- Basic Attributes --}}
                                <div class="col-12">
                                    <hr />
                                    <h6 class="text-muted mb-3">Basic Attributes</h6>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Age:</strong></label>
                                    <p>{{ $generatedCharacter->age ?? '—' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Gender:</strong></label>
                                    <p>{{ $generatedCharacter->gender ? ucfirst($generatedCharacter->gender) : '—' }}</p>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Ethnicity:</strong></label>
                                    <p>{{ $generatedCharacter->ethnicity ? ucfirst($generatedCharacter->ethnicity) : '—' }}</p>
                                </div>

                                {{-- Advanced Attributes --}}
                                <div class="col-12">
                                    <hr />
                                    <h6 class="text-muted mb-3">Advanced Attributes</h6>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Eye Color:</strong></label>
                                    <p>{{ $generatedCharacter->eye_color ? ucfirst($generatedCharacter->eye_color) : '—' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Build:</strong></label>
                                    <p>{{ $generatedCharacter->build ? ucfirst($generatedCharacter->build) : '—' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Style:</strong></label>
                                    <p>{{ $generatedCharacter->style ? ucfirst($generatedCharacter->style) : '—' }}</p>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label"><strong>Expression:</strong></label>
                                    <p>{{ $generatedCharacter->expression ? ucfirst($generatedCharacter->expression) : '—' }}</p>
                                </div>

                                {{-- Custom Modification --}}
                                <div class="col-12">
                                    <label class="form-label"><strong>Custom Modification:</strong></label>
                                    <p>{{ $generatedCharacter->custom_modification ?? '—' }}</p>
                                </div>

                                {{-- Error Message --}}
                                @if ($generatedCharacter->error_message)
                                <div class="col-12">
                                    <label class="form-label"><strong>Error Message:</strong></label>
                                    <p class="text-danger">{{ $generatedCharacter->error_message }}</p>
                                </div>
                                @endif

                                {{-- Generated Image --}}
                                <div class="col-12">
                                    <hr />
                                    <label class="form-label"><strong>Generated Character Image:</strong></label>
                                    @if ($generatedCharacter->generated_image_path)
                                        <div class="mt-3">
                                            <img src="{{ asset('storage/' . $generatedCharacter->generated_image_path) }}"
                                                alt="Generated Character Image"
                                                style="max-width: 300px; max-height: 300px; object-fit: cover; border-radius: 8px;" />
                                        </div>
                                    @else
                                        <p class="text-muted">No image generated yet.</p>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="col-12 mt-3">
                                    <a href="{{ route('admin.generated-characters.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
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
