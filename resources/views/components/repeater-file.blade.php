<div>
    @if ($hasLabel)
        <label for="{{ $repeaterName }}_{{ $id ?? $name }}">{{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</label>
    @endif
    <div class="custom-file">
        <input type="file"
            @if ($multiple) multiple name="{{ $repeaterName }}[][{{ $name }}][]" @else name="{{ $repeaterName }}[][{{ $name }}]" @endif
            {!! $attrs !!} class="custom-file-input {{ $class }}"
            id="{{ $repeaterName }}_{{ $id ?? $name }}">
        <label class="custom-file-label" for="{{ $repeaterName }}_{{ $id ?? $name }}">Choose
            {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</label>
        <div class="invalid-tooltip">Please provide a valid {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</div>
    </div>
</div>
