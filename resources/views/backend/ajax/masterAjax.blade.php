{{-- For AJAX --}}
{{-- jQuery is already loaded earlier in the layout --}}
{{-- For Alert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showToast({
        icon = 'success',
        title = 'Action Successful',
        background = '#D4EDDA',
        position = 'top-end',
        timer = 3000
    } = {}) {
        const Toast = Swal.mixin({
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-xl shadow-lg text-sm font-semibold'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: icon,
            title: title,
            background: background,
        });
    }
</script>

@include('modal._toast_modal')
@include('modal._loaded_modal')


<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name = "csrf-token"]').attr('content')
        }
    })
</script>