<script>
    $(document).ready(function() {
        const dt = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            stateSave: true,
            searching: true,
            paging: true,
            info: false,
            ajax: '{{ route('dynamic_page.index') }}',
            order: [[0, 'desc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'page_title', orderable: true, searchable: true },
                { data: 'page_content', orderable: true, searchable: true },
                { data: 'status', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false },
            ],
            language: {
                paginate: {
                    previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>',
                    next: '<i class="fa fa-angle-right" aria-hidden="true"></i>'
                },
                processing: dataTableLoader()
            },
            columnDefs: [
                { targets: [0, 4], className: 'text-center' }
            ]
        });

        // Open Status Modal
        $(document).on('click', '.change_status', function (e) {
            e.preventDefault();
            $('#status_id').val($(this).data('id'));
            $('#status_enabled').val($(this).data('enabled'));
            $('#status_title').text($(this).data('title'));
            $('#status_description').text($(this).data('description'));
        });
        // Submit Status Change
        $('#status_form').on('submit', function (e) {
            e.preventDefault();
            const id = $('#status_id').val();
            const status = $('#status_enabled').val();

            $.ajax({
                url: '{{ url('/admin/dynamic-page') }}/' + id + '/status',
                type: 'PATCH',
                data: {
                    status: status,
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    dt.ajax.reload(null, false);
                    successModal(res.message || 'Status Updated');
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    errorModal();
                }
            });
        });

        // Delete Confirmation
        $(document).on('click', '.deletebtn', function (e) {
            e.preventDefault();
            $('#delete_id').val($(this).data('id'));
            $('#deletemodal').modal('show');
        });

        $('#delete_modal_clear').on('submit', function (e) {
            e.preventDefault();
            const id = $('#delete_id').val();

            $.ajax({
                url: '{{ url('/admin/dynamic-page') }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    dt.ajax.reload(null, false);
                    successModal(res.message || 'Deleted Successfully');
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    errorModal();
                }
            });
        });

    });
</script>