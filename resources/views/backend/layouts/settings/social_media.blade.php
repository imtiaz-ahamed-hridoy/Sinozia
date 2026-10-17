@extends('backend.app')
@section('title', 'Social Media Settings')
@push('style')
<style>
    .social-media-field {
        width: 100%;
        margin-bottom: 15px;
    }

    .social-media-field .form-select,
    .social-media-field .form-control {
        min-height: 44px;
    }

    .remove-btn {
        min-height: 43px;
        min-width: auto;
        white-space: nowrap;
    }

    .add-btn-container {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
    }

</style>
@endpush
@section('page-content')

<x-breadcrumbs title="Social Media Settings" subtitle="" />

<!-- Container-fluid starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <x-table-header title="Social Media Settings" subtitle="Manage your social media links and profiles." />

                <div class="card-body">
                    <div class="card-wrapper border rounded-3 p-3">
                        <form action="{{ route('social.update') }}" method="POST">
                            @csrf
                            <div class="add-btn-container">
                                <button class="btn btn-primary btn-sm" type="button" onclick="addSocialField()" title="Add a new social media field"><i class="fa fa-plus-circle" aria-hidden="true"></i> Add New</button>
                            </div>

                            <div id="social_media_container">
                                @foreach ($social_link as $index => $link)
                                <div class="mb-3 social_media1 input-group dropdown">
                                    <input type="hidden" name="social_media_id[]" value="{{ $link->id }}">
                                    <select class="border dropdown-toggle" name="social_media[]" value="@isset($social_link){{ $link->social_media }}@endisset" title="Select a social media platform">
                                        <option class="dropdown-item">Select Social</option>
                                        <option class="dropdown-item" value="facebook" {{ $link->social_media == 'facebook' ? 'selected' : '' }}>Facebook
                                        </option>
                                        <option class="dropdown-item" value="instagram" {{ $link->social_media == 'instagram' ? 'selected' : '' }}>Instagram
                                        </option>
                                        <option class="dropdown-item" value="twitter" {{ $link->social_media == 'twitter' ? 'selected' : '' }}>Twitter
                                        </option>
                                        <option class="dropdown-item" value="linkedin" {{ $link->social_media == 'linkedin' ? 'selected' : '' }}>Linkedin
                                        </option>
                                    </select>
                                    <input type="url" class="form-control" aria-label="Text input with dropdown button" name="profile_link[]" value="@isset($social_link){{ $link->profile_link }}@endisset" placeholder="Enter the profile link here">
                                    <button class="btn btn-outline-danger removeSocialBtn" type="button" style="font-weight: 900" data-id="{{ $link->id }}" title="Remove this social media field"><i class="fa fa-trash" aria-hidden="true"></i> Remove</button>

                                </div>
                                @endforeach

                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" title="Cancel and go back to the dashboard">
                                            <i class="fa fa-arrow-left"></i> Back
                                        </a>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fa fa-save"></i> Save
                                        </button>
                                    </div>
                                </div>
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

@push('script')
<script>
    let socialFieldsCount = $('#social_media_container .social_media1').length;

    // Open delete modal
    $(document).on('click', '.removeSocialBtn', function() {
        $('#delete_id').val($(this).data('id'));
        $('#deletemodal').modal('show');
    });

    // Handle delete confirm
    $('#delete_modal_clear').on('submit', function(e) {
        e.preventDefault();
        let id = $('#delete_id').val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , type: 'DELETE'
            , url: '{{ route('social.delete', ':id') }}'.replace(':id', id)
            , data: {
                _token: '{{ csrf_token() }}'
            }
            , success: function(response) {
                if (response.success) {
                    $('button[data-id="' + id + '"]').closest('.social_media1').remove();
                    socialFieldsCount--;
                    $('#deletemodal').modal('hide');
                    successModal(response.message || 'Deleted successfully');
                } else {
                    errorModal('Deletion failed');
                }
            }
            , error: function() {
                errorModal('An error occurred');
            }
        });
    });

    // Add new social field
    function addSocialField() {
        if (socialFieldsCount >= 4) {
            errorModal("You can only add four social links fields!");
            return;
        }
        const container = document.getElementById("social_media_container");
        const newField = `
                            <div class="mb-3 social_media1 input-group dropdown">
                                <select class="border dropdown-toggle" name="social_media[]">
                                    <option>Select Social</option>
                                    <option value="facebook">Facebook</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="twitter">Twitter</option> 
                                    <option value="linkedin">Linkedin</option>                                 
                                </select>
                                <input type="url" class="form-control" name="profile_link[]" placeholder="Enter profile link" aria-label="Text input with dropdown button">
                                <button class="btn btn-outline-danger" title="Remove this social media field" type="button" onclick="removeNewSocialField(this)"><i class="fa fa-trash" aria-hidden="true"></i> Remove</button>



                            </div>`;
        container.insertAdjacentHTML("beforeend", newField);
        socialFieldsCount++;
        attachDuplicateCheck();
    }

    function removeNewSocialField(button) {
        button.closest('.social_media1').remove();
        socialFieldsCount--;
        attachDuplicateCheck();
    }

    function attachDuplicateCheck() {
        document.querySelectorAll('select[name="social_media[]"]').forEach(select => {
            select.removeEventListener('change', checkForDuplicateSocialMedia);
            select.addEventListener('change', checkForDuplicateSocialMedia);
        });
    }

    function checkForDuplicateSocialMedia() {
        const allValues = Array.from(document.querySelectorAll('select[name="social_media[]"]')).map(s => s.value);
        const duplicates = allValues.filter((v, i) => allValues.indexOf(v) !== i && v !== "Select Social");
        if (duplicates.length) {
            errorModal("Duplicate social media platform not allowed.");
            document.querySelectorAll('select[name="social_media[]"]').forEach(select => {
                if (duplicates.includes(select.value)) select.value = "Select Social";
            });
        }
    }

    $(document).ready(function() {
        attachDuplicateCheck();
    });

</script>


@endpush
