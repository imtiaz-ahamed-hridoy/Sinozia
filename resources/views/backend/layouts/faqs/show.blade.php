@extends('backend.app')
@section('title', 'FAQ Details')

@section('page-content')

<x-breadcrumbs title="FAQs" subtitle="FAQ Details" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card shadow-sm border-0">
                <x-table-header :title="'FAQ Details'" subtitle="View FAQ question and answer" />
                <div class="card-body">
                    <div class="faq-details-wrapper p-4 bg-light rounded-4 border">
                        <!-- Question Section -->
                        <div class="d-flex align-items-start mb-4 pb-4 border-bottom">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px; flex-shrink: 0;">
                                <i data-feather="help-circle"></i>
                            </div>
                            <div class="pt-1">
                                <h6 class="text-uppercase text-muted fw-bold mb-1" style="font-size: 0.85rem; letter-spacing: 0.5px;">Question</h6>
                                <h4 class="mb-0 fw-bold text-dark" style="line-height: 1.4;">{{ $faq->question }}</h4>
                            </div>
                        </div>

                        <!-- Answer Section -->
                        <div class="d-flex align-items-start mb-2">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 50px; height: 50px; flex-shrink: 0;">
                                <i data-feather="message-circle"></i>
                            </div>
                            <div class="pt-1 w-100">
                                <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Answer</h6>
                                <div class="text-secondary p-4 bg-white rounded-3 shadow-sm border-start border-4 border-success" style="font-size: 1.05rem; line-height: 1.8;">
                                    {{ $faq->answer }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-end mt-4">
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary" title="Cancel and go back to the dashboard">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-primary" title="Edit this FAQ">
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
