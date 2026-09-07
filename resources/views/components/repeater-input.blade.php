<div class="form-group ">
    <label for="{{ $repeaterName . '_' . $name }}">{{ Str::ucfirst($name) }}</label>
    <input type="{{ $type ?? 'text' }}" class="form-control" id="{{ $repeaterName . '_' . $name }}"
        name="{{ $repeaterName }}[][{{ $name }}]" @if ($required == 'true') required @endif
        placeholder="Enter {{ $name }}">
    <div class="invalid-tooltip">Please provide a valid {{ $name }}</div>
</div>
