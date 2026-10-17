<div class="modal fade bd-example-modal-sm" id="{{ $modalId ?? 'statusModal' }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content modal_icon border-0 shadow-lg rounded-4">
            <form id="{{ $formId ?? 'status_form' }}">
                @csrf
                <input type="hidden" id="status_id">
                <input type="hidden" id="status_enabled">

                <div class="modal-body text-center p-4">
                    <img src="{{ asset('backend/assets/icon/warning.png') }}" class="img-fluid mb-3" alt="Warning" style="max-height: 60px;">
                    <h5 id="status_title" class="mb-2 fw-bold text-dark"></h5>
                    <p id="status_description" class="text-muted mb-0" style="font-size: 0.9rem;"></p>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-center pb-4 pt-0 gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger btn-sm px-4 rounded-pill" data-bs-dismiss="modal">Yes</button>
                </div>
            </form>
        </div>
    </div>
</div>