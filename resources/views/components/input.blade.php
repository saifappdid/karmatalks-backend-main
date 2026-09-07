<div class="form-group {{ $parentClass }} text-left">
    @if ($hasLabel && $type !== 'hidden')
        <label for="{{ $id ?? $name }}">{!! $label ?? Str::ucfirst(Str::replace('_', ' ', $name)) !!}</label>
    @endif
    @if ($type == 'textarea')
        <textarea @if ($required) required @endif {!! $attrs !!} class="form-control"
            id="{{ $id ?? $name }}" name="{{ $name }}" rows="3"
            placeholder=" {{ $placeholder ?? 'Enter ' . Str::replace('_', ' ', $name) }}">{{ $value }}</textarea>
        @if (!empty($helperText))
            <p><small class="text-muted">{{ $helperText }}</small></p>
        @endif
        <div class="invalid-tooltip">Please provide a valid {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</div>
    @else
        <input value="{{ $value }}" type="{{ $type }}" class="form-control {{ $class }}"
            @if ($maxLength) maxlength="{{ $maxLength }}" @endif
            @if ($pattern) pattern="{{ $pattern }}" @endif
            @if ($required) required @endif name="{{ $name }}" {!! $attrs !!}
            id="{{ $id ?? $name }}" placeholder=" {{ $placeholder ?? 'Enter ' . Str::replace('_', ' ', $name) }}">
        @if (!empty($helperText))
            <p><small class="text-muted">{{ $helperText }}</small></p>
        @endif
        <div class="invalid-tooltip">Please provide a valid {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</div>

    @endif
</div>
