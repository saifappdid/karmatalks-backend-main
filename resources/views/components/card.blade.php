<div id="{{ $id }}" class="card {{ $class }}">
    @isset($title)
        <div class="card-header text-center justify-content-center">
            <h4 class="card-title">{{ $title }}</h4>

        </div>
    @endisset
    <div class="card-body">
        {{ $slot }}
    </div>
</div>
