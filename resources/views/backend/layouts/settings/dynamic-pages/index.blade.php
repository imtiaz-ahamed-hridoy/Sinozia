@extends('backend.app')
@section('title', 'Dynamic Pages')
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

<x-breadcrumbs title="Dynamic Pages" subtitle="" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header title="Dynamic Pages" :route="route('dynamic_page.create')" buttonText="Add New" subtitle="List of all dynamic pages" />

                <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                        <div id="basic-1_wrapper" class="dataTables_wrapper no-footer">
                            <table class="dataTable no-footer hover" id="data-table" role="grid" aria-describedby="basic-1_info">
                                <thead class="border-bottom">
                                    <tr role="row">
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Page Content</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Container-fluid Ends-->
@endsection

@section('modal')
<x-status-modal />
@endsection


@push('script')
@include('backend.layouts.settings.dynamic-pages.partials._dynamicPageJS')
@endpush
