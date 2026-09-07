{{-- <button type="button" class="btn btn-primary waves-effect waves-float waves-light">
    Primary
</button> --}}
<button @if (!empty($isSubmit)) type="submit" @else type="button" @endif {!! $attrs !!}
    id="{{ $id }}"
    class="btn btn-{{ $type ?? 'primary' }} waves-effect waves-float waves-light {{ $class }}">
    {{ $text }} {{ $icon }}
</button>
