@php
$uuid = Str::uuid();
@endphp

@if ($modal)
    <x-modal title="Map" footer="false" size="xl" id="{{ $uuid }}">
        <x-slot name="body">
            <div id="map"></div>
        </x-slot>
    </x-modal>
@else
    <div id="map"></div>
@endif

@pushonce('component-style')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" />
@endpushonce

@pushonce('component-script')
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"></script>
@endpushonce

@push('component-script')
    <script>
        @if ($modal)
            $(document).on('focus', '{{ $linked_to }}', (function(e) {
                e.preventDefault();
                $('#{{ $uuid }}').modal('show');

            }));
            $("#{{ $uuid }}").on('shown.bs.modal', function() {
                map.invalidateSize();
            });
        @endif
        var map;
        var pin;
        var tilesURL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
        var mapAttrib = '';

        MapCreate();

        function MapCreate() {

            if (!(typeof map == "object")) {

                map = L.map('map', {
                    center: [19.198585352647367, 72.95576384864164],
                    zoom: 15
                });


            } else {
                map.setZoom(15).panTo([19.198585352647367, 72.95576384864164]);
            }

            L.tileLayer(tilesURL, {
                attribution: mapAttrib,
                maxZoom: 19
            }).addTo(map);
        }

        map.on('click', function(ev) {

            $('{{ $linked_to }}').val(ev.latlng.lat + ',' + ev.latlng.lng);
            if (typeof pin == "object") {
                pin.setLatLng(ev.latlng);
            } else {
                pin = L.marker(ev.latlng, {
                    riseOnHover: true,
                    draggable: true
                });
                pin.addTo(map);
                pin.on('drag', function(ev) {
                    $('{{ $linked_to }}').val(ev.latlng.lat + ',' + ev.latlng.lng);
                });
            }
        });
    </script>
@endpush
