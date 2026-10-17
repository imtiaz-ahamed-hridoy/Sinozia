@extends('backend.app')
@section('title', isset($faq) ? 'Edit FAQ' : 'Create New FAQ')
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

<x-breadcrumbs title="FAQs" subtitle="{{ isset($faq) ? 'Edit FAQ' : 'Create New FAQ' }}" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="isset($faq) ? 'Edit FAQ' : 'Create New FAQ'" subtitle="" />
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" action="{{ isset($faq) ? route('admin.faqs.update', $faq->id) : route('admin.faqs.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($faq))
                            @method('PATCH')
                            @endif
                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Question</label>
                                <input class="form-control  @error('question') is-invalid @enderror" id="inputEmail4" type="text" placeholder="Enter Your Question" value="{{ isset($faq) ? $faq->question : '' }}" name="question">
                                @error('question')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="summernote">Answer</label>

                                <textarea class="form-control  @error('answer') is-invalid @enderror" type="text" placeholder="Enter Your Answer" name="answer" rows="4">{{ isset($faq) ? $faq->answer : '' }}</textarea>
                                @error('answer')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ isset($faq) ? 'Update' : 'Create' }}</button>
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
