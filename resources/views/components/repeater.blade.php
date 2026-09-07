<div class="col-12" id="container-{{ $name }}">
    <div class="row">

        <div class="col-12">
            <div class="row container-item-{{ $name }}">
                @forelse ($fields as $f)
                    @if (empty($f['type']))
                        @php
                            $f['type'] = 'text';
                        @endphp
                    @endif

                    @if ($f['type'] === 'file')
                        <div class="col-md-{{ $f['col'] ?? 6 }} col-12 ">
                            <x-repeater-file required="{{ $f['required'] ?? 'true' }}" repeaterName="{{ $name }}"
                                :multiple="$f['multiple'] ?? false" name="{{ $f['name'] }}" />
                        </div>
                    @elseif ($f['type'] !== 'select')
                        <div class="col-md-{{ $f['col'] ?? 6 }} col-12 ">
                            <x-repeater-input required="{{ $f['required'] ?? 'true' }}"
                                type="{{ $f['type'] ?? 'text' }}" repeaterName="{{ $name }}"
                                name="{{ $f['name'] }}" />
                        </div>
                    @endif

                    @if ($f['type'] === 'select')
                        <div class="col-md-{{ $f['col'] ?? 6 }} col-12 ">
                            <x-repeater-select name="{{ $f['name'] }}" repeaterName="{{ $name }}"
                                :options="$f['options']" :multiple="$f['multiple'] ?? false" />
                        </div>
                    @endif
                @empty
                @endforelse
                {{ $data ?? '' }}

                <div class="col-md-2 col-12 d-flex align-items-center">
                    <div class="form-group m-0">
                        <button class="btn btn-outline-danger text-nowrap px-1 remove-item-{{ $name }}"
                            type="button">
                            <i data-feather="x" class="mr-25"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-12 text-center mt-2">
    <button id="add-more-{{ $name }}" type="button" class="btn btn-outline-primary ">
        Add
    </button>
</div>

@pushonce('component-vendor-script')
    <script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
@endpushonce
@push('component-script')
    <script>
        // jQuery cloneData.js
        $('#add-more-{{ $name }}').cloneData({
            mainContainerId: 'container-{{ $name }}',
            cloneContainer: 'container-item-{{ $name }}',
            removeButtonClass: 'remove-item-{{ $name }}',
            removeConfirm: true,
            removeConfirmMessage: 'Are you sure want to delete?',
            append: '',
            maxLimit: {{ $maxLimit }},
            minLimit: 1,
            defaultRender: 1,
            afterRender: function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            },
        });
    </script>
@endpush
