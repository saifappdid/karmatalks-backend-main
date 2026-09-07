@if (!empty($label))
    <label for="{{ $name }}">{{ $label }}</label>
@endif
<div id="{{ $id ?? $name }}">
    <div class="editor">
        {{ $slot }}
    </div>
    <input data-{{ $id ?? $name }} type="hidden" name="{{ $name }}">
</div>
@pushonce('component-style')
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/katex.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/monokai-sublime.min.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.snow.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('vendors/css/editors/quill/quill.bubble.css')) }}">
    <link rel="stylesheet" href="{{ asset(mix('css/base/plugins/forms/form-quill-editor.css')) }}">
    <style>
        .invalid-editor {
            border: solid 1px #ea5455;
            border-radius: 5px;
        }
    </style>
@endpushonce

@pushonce('component-script')
    <script src="{{ asset(mix('vendors/js/editors/quill/katex.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/highlight.min.js')) }}"></script>
    <script src="{{ asset(mix('vendors/js/editors/quill/quill.min.js')) }}"></script>
@endpushonce
@push('component-script')
    <script>
        const fullEditor_{{ $id ?? $name }} = new Quill('#{{ $id ?? $name }} .editor', {
            bounds: '#{{ $id ?? $name }} .editor',
            modules: {
                formula: true,
                syntax: true,
                toolbar: [
                    [{
                            font: []
                        },
                        {
                            size: []
                        }
                    ],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                            color: []
                        },
                        {
                            background: []
                        }
                    ],
                    [{
                            script: 'super'
                        },
                        {
                            script: 'sub'
                        }
                    ],
                    [{
                            header: '1'
                        },
                        {
                            header: '2'
                        },
                        'blockquote',
                        'code-block'
                    ],
                    [{
                            list: 'ordered'
                        },
                        {
                            list: 'bullet'
                        },
                        {
                            indent: '-1'
                        },
                        {
                            indent: '+1'
                        }
                    ],
                    [
                        'direction',
                        {
                            align: []
                        }
                    ],
                    ['link', 'image', ],
                    ['clean']
                ]
            },
            theme: 'snow'
        });
        fullEditor_{{ $id ?? $name }}.on('text-change', function(delta, oldDelta, source) {
            $('input[data-{{ $id ?? $name }}]').val(fullEditor_{{ $id ?? $name }}.root.innerHTML);
            if ($('[data-{{ $id ?? $name }}').val() == '<p><br></p>') {
                $('#{{ $id ?? $name }}').addClass('invalid-editor');
            } else {
                $('#{{ $id ?? $name }}').removeClass('invalid-editor');
            }
        });
    </script>
@endpush
