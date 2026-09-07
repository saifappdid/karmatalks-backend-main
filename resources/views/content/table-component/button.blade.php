<button type="button" @forelse ($data as $key=> $d) {{ 'data-' . $key . '=' . $d }}@empty @endforelse
    class="btn {{ $class ?? 'btn-flat-success' }} waves-effect">
    <span>{{ $text ?? '' }} {!! $icon ?? '' !!}</span>
</button>
