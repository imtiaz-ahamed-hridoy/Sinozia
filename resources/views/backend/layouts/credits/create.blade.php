@extends('backend.app')
@section('title', isset($credit) ? 'Edit Credit' : 'Create New Credit')

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

<x-breadcrumbs title="Credits" subtitle="{{ isset($credit) ? 'Edit Credit' : 'Create New Credit' }}" />


<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header :title="isset($credit) ? 'Edit Credit' : 'Create New Credit'" subtitle="" />
                <div class="card-body">
                    <div class="card-wrapper border rounded-3">
                        <form class="row g-3" action="{{ isset($credit) ? route('admin.credits.update', $credit->id) : route('admin.credits.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($credit))
                            @method('PATCH')
                            @endif
                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Credit</label>
                                <input class="form-control  @error('credit') is-invalid @enderror" id="inputEmail4" type="text" placeholder="Enter Your Credit" value="{{ isset($credit) ? $credit->credit : '' }}" name="credit">
                                @error('credit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="inputEmail4">Amount</label>
                                <input class="form-control  @error('amount') is-invalid @enderror" id="inputEmail4" type="text" placeholder="Enter Credit Amount" value="{{ isset($credit) ? $credit->amount : '' }}" name="amount">
                                @error('amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="col-12">
                                <a href="{{ route('admin.credits.index') }}" class="btn btn-secondary"><i class="fa fa-arrow-left"></i> Back</a>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> {{ isset($credit) ? 'Update' : 'Create' }}</button>
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
