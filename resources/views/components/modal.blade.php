<div class="modal {{ $animation }}" id="{{ $id }}" tabindex="-1" role="dialog"
    aria-labelledby="{{ $id }}Title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-{{ $size }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                {{ $slot ?? ($body ?? '') }}
            </div>
            @if ($footer)
                <div class="modal-footer">
                    <button type="button" class="btn btn-{{ $btn }} waves-effect waves-float waves-light"
                        data-dismiss="modal">{{ $btnText }}</button>
                </div>
            @endif
        </div>
    </div>
</div>
