<script>
    function loadedModal(value) {
        const spinner = '<button class="btn btn-primary me-2 " type="button" disabled>\
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true">\
                    </span> ' + value + '...\
                </button>'
        return spinner;
    }

    // loader-component.js
    function dataTableLoader(color = "text-success", size = "3rem") {
        return `
        <div class="text-center">
            <div class="spinner-border ${color}" style="width: ${size}; height: ${size};" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    }
</script>
