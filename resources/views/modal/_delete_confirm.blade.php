<div class="modal fade bd-example-modal-sm" id="deletemodal" tabindex="-1" role="dialog" aria-labelledby="deletemodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content modal_icon border-0 shadow-lg rounded-4">
            <form action="" method="post" id="delete_modal_clear">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <input type="hidden" name="delete_type" id="delete_type">

                <div class="modal-body text-center p-4">
                    <img src="{{ asset('backend/assets/icon/delete-icon.png') }}" class="img-fluid mb-3" alt="Delete Icon" style="max-height: 60px;">
                    <h5 class="mb-2 fw-bold text-dark">Do you want to Delete?</h5>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">You won't be able to recover it!</p>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-center pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 rounded-pill delete_data" data-bs-dismiss="modal">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
