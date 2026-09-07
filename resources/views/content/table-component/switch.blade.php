@php
    if (empty($column)) {
        $status = $data->status == 'active' ? 'checked' : '';
    } else {
        $status = $data->$column == 'active' ? 'checked' : '';
    }
@endphp
<div class="custom-control custom-control-success custom-switch">
    <input data-block="tr" data-route="{{ $route }}" value="{{ $data->id }}" type="checkbox" {{ $status }}
        class="custom-control-input status-switch {{ $class ?? '' }}" id="switch-{{ $id ?? '' }}{{ $data->id }}">
    <label class="custom-control-label" for="switch-{{ $id ?? '' }}{{ $data->id }}"></label>
</div>
