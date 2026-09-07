<x-button text="view" attrs='data-id="{{ $id }}" data-viewer-btn' />

<x-modal id="data-viewer-{{ $id }}" :title="$title" size="lg">
    <x-slot name="body">
        {!! $body ?? '' !!}
    </x-slot>
</x-modal>
