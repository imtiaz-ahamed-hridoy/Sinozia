<ul class="action d-flex justify-content-center align-items-center list-unstyled mb-0 gap-2">
    <!-- View Details -->
    @if (isset($show))
    <li class="view"> <a href="{{ route($show, $id) }}" title="View Details"><i class="fa fa-eye" aria-hidden="true"></i></a></li>
    @endif

    <!-- Edit -->
    @if (isset($edit))
    <li class="edit"> <a href="{{ route($edit, $id) }}" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a></li>
    @endif

    <!-- Delete -->
    @if (isset($delete) && $delete)
    <li class="delete"><a href="#" title="Delete" class="deletebtn" data-id="{{ $id }}"><i class="fa fa-trash-o" aria-hidden="true"></i></a></li>
    @endif
</ul>
