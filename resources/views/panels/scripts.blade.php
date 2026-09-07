{{-- Vendor Scripts --}}
<script src="{{ asset(mix('vendors/js/vendors.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/ui/prism.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/toastr.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/moment.min.js')) }}"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.6/dist/notiflix-aio-3.2.6.min.js
"></script>
@livewireScripts()

@if (!empty($pageConfigs['has_table']))
    @if ($pageConfigs['has_table'])
        <script src="{{ asset(mix('vendors/js/tables/datatable/jquery.dataTables.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.bootstrap4.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/dataTables.responsive.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/responsive.bootstrap4.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.checkboxes.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/datatables.buttons.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/jszip.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/pdfmake.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/vfs_fonts.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.html5.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/tables/datatable/buttons.print.min.js')) }}"></script>
        @isset($dataTable)
            {!! $dataTable->scripts() !!}
        @endisset
    @endif
@endif
@if (!empty($pageConfigs['has_editor']))
    @if ($pageConfigs['has_editor'])
        <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
    @endif
@endif


<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@if (!empty($pageConfigs['has_player']))
    @if ($pageConfigs['has_player'])
        <script src="{{ asset(mix('vendors/js/extensions/plyr.min.js')) }}"></script>
        <script src="{{ asset(mix('vendors/js/extensions/plyr.polyfilled.min.js')) }}"></script>
    @endif
@endif

@if (!empty($pageConfigs['has_chart']))
    @if ($pageConfigs['has_chart'])
        <script src="{{ asset(mix('vendors/js/charts/apexcharts.min.js')) }}"></script>
    @endif
@endif

@stack('component-vendor-script')


@yield('vendor-script')
{{-- Theme Scripts --}}
<script src="{{ asset(mix('js/core/app-menu.js')) }}"></script>
<script src="{{ asset(mix('js/core/app.js')) }}"></script>
<script src="{{ asset(mix('js/init.js')) }}"></script>

@if (!empty($pageConfigs['has_editor']))
    @if ($pageConfigs['has_editor'])
        <script src="{{ asset(mix('js/scripts/forms/form-quill-editor.js')) }}"></script>
    @endif
@endif
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/pickers/flatpickr/flatpickr.min.js')) }}"></script>


@if ($configData['blankPage'] === false)
@endif
{{-- page script --}}
@stack('component-script')
@yield('page-script')
{{-- page script --}}
<script>
    $(document).ready(function () {
        $(window).on('table-refreshed', function () {
            alert('');
        });
    });
</script>