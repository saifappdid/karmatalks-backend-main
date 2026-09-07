<div class="modal modal-slide-in fade {{ $class }}" id="{{ $id }}">
    <div class="modal-dialog sidebar-lg {{ $modalWidth }}">
        <div class="modal-content p-0">

            <div class="modal-header align-items-center mb-1">
                <h5 class="modal-title">{{ $title ?? 'Heading' }}</h5>
                <div class="todo-item-action d-flex align-items-center justify-content-between ml-auto">
                    <button type="button" class="close font-large-1 font-weight-normal py-0" data-dismiss="modal"
                        aria-label="Close">
                        ×
                    </button>
                </div>
            </div>
            <div class="modal-body flex-grow-1 pb-sm-0 pb-3">

                {{ $slot ?? '' }}

            </div>

        </div>
    </div>
</div>
