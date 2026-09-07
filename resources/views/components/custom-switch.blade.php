<div class="custom-control custom-control-primary custom-switch {{ $parentClass }}">
    @if ($label !== '')
        <p class="mb-50">{{ $label }}</p>
    @endif
    <input name="{{ $name ?? '' }}" @if ($disabled) disabled @endif type="checkbox"
        @if ($checked) checked @endif class="custom-control-input {{ $class }}"
        id="{{ $id }}">
    <label class="custom-control-label" for="{{ $id }}"></label>
</div>

@if (!empty($syncTo))
    @push('component-script')
        <script>
            $(document).ready(function() {
                $('#{{ $id }}').change(function() {
                    if ($(this).is(':checked')) {
                        $('{{ $syncTo }}').val('on');
                    } else {
                        $('{{ $syncTo }}').val('off');
                    }
                });
            });
        </script>
    @endpush
@endif
