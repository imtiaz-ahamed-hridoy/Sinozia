@extends('backend.app')
@section('title', isset($targetVoice) ? 'Edit Target Voice' : 'Create New Target Voice')

@section('page-content')

    <x-breadcrumbs title="Target Voices"
        subtitle="{{ isset($targetVoice) ? 'Edit Target Voice' : 'Create New Target Voice' }}" />

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <x-table-header :title="isset($targetVoice) ? 'Edit Target Voice' : 'Create New Target Voice'" subtitle="" />
                    <div class="card-body">
                        <div class="card-wrapper border rounded-3">
                            <form class="row g-3"
                                action="{{ isset($targetVoice) ? route('admin.target-voices.update', $targetVoice->id) : route('admin.target-voices.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($targetVoice))
                                    @method('PATCH')
                                @endif

                                {{-- Title --}}
                                <div class="col-md-12">
                                    <label class="form-label" for="title">
                                        Title <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control @error('title') is-invalid @enderror" id="title"
                                        type="text" placeholder="Enter Target Voice Title"
                                        value="{{ old('title', $targetVoice->title ?? '') }}" name="title">
                                    @error('title')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                {{-- ElevenLabs Voice ID --}}
                                <div class="col-md-12">
                                    <label class="form-label" for="elevenlabs_voice_id">
                                        ElevenLabs Voice ID <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control @error('elevenlabs_voice_id') is-invalid @enderror"
                                        id="elevenlabs_voice_id" type="text" placeholder="e.g. 21m00Tcm4TlvDq8ikWAM"
                                        value="{{ old('elevenlabs_voice_id', $targetVoice->elevenlabs_voice_id ?? '') }}"
                                        name="elevenlabs_voice_id">
                                    <p class="form-text">
                                        Find this in your
                                        <a href="https://elevenlabs.io/app/voice-lab" target="_blank">
                                            ElevenLabs dashboard
                                        </a>
                                        under Voices. Each voice has a unique ID string.
                                    </p>
                                    @error('elevenlabs_voice_id')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                {{-- Audio Preview File --}}
                                <div class="col-md-12">
                                    <label class="form-label" for="audio">
                                        Preview Audio
                                        <span class="text-muted">(optional — played in app before user selects)</span>
                                    </label>
                                    <input class="form-control @error('audio') is-invalid @enderror" id="audio"
                                        type="file" name="audio" accept=".mp3,.wav,.ogg,.m4a">
                                    <p class="form-text">
                                        Allowed: mp3, wav, ogg, m4a. Max 100 MB.
                                        This file is only used as an in-app preview — it is not sent to ElevenLabs.
                                    </p>

                                    {{-- Show existing audio if editing --}}
                                    @if (isset($targetVoice) && $targetVoice->audio)
                                        <div class="mt-2">
                                            <label class="form-label">
                                                <strong>Current Preview Audio:</strong>
                                            </label>
                                            <div>
                                                <audio controls style="width: 300px;">
                                                    <source src="{{ asset('storage/' . $targetVoice->audio) }}"
                                                        type="audio/mpeg">
                                                    Your browser does not support the audio element.
                                                </audio>
                                            </div>
                                        </div>
                                    @endif

                                    @error('audio')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="col-12">

                                    <a href="{{ route('admin.target-voices.index') }}" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa fa-save"></i>
                                        {{ isset($targetVoice) ? 'Update' : 'Create' }}
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
