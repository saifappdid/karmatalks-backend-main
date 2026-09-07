@php
$columns = explode(',', $columns);
@endphp
<table
    class="table table-borderless table-hover-animation @isset($class) {{ $class }} @endisset">
    <thead>
        <tr>
            @isset($columns)
                @forelse ($columns as $col)
                    <th>{{ Str::replace('_', ' ', $col) }}</th>
                @empty
                @endforelse
            @endisset


        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
@push('component-script')
    <script>
        @if (!empty($columns))
            const col = JSON.parse('{!! json_encode($columns) !!}');
            (() => {
            initYtable({
            url: "{{ $route }}",
            col: col,
            loadingText: "{{ $loadingText }}",
            });
            })();
        @else
            alert('No columns defined , Cant initialize table');
        @endif
    </script>
@endpush
