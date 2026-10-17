@extends('backend.app')
@section('title', 'Generated Characters Management')

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

<x-breadcrumbs title="Generated Characters" subtitle="" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header title="Generated Characters" subtitle="List of all user-generated AI characters" />
                <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                        <div id="basic-1_wrapper" class="dataTables_wrapper no-footer">
                            <table class="dataTable no-footer hover" id="data-table" role="grid"
                                aria-describedby="basic-1_info">
                                <thead class="border-bottom">
                                    <tr role="row">
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Attributes</th>
                                        <th>Generated Image</th>
                                        <th>Status</th>
                                        <th>Active</th>
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
@include('backend.layouts.generated-characters.partials._generatedCharacterJS')
@endpush
