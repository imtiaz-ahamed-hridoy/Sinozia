@extends('frontend.app')
@section('title', "$dynamicPage->page_title")
@push('style')

<style>
    .card:hover {
        border: none;
        box-shadow: none;
    }

</style>


@endpush

@section('content')
<section>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0" style="padding-left: 20px;">

                    <div class="card-header text-center">
                        <h2>{{ $dynamicPage->page_title }}</h2>
                    </div>
                    <div class="card-body">
                        <p class="mt-4 lead">{!! $dynamicPage->page_content !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
