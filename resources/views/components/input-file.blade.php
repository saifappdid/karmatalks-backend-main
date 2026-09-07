<div class="form-group {{ $parentClass }}">
    @if ($hasLabel)
        <label for="{{ $id ?? $name }}">{{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</label>
    @endif
    <div class="custom-file">
        <input type="file"
            @if ($multiple) multiple name="{{ $name }}[]" @else name="{{ $name }}" @endif
            {!! $attrs !!} class="custom-file-input {{ $class }}" id="{{ $id ?? $name }}">
        <label class="custom-file-label" for="{{ $id ?? $name }}">Choose
            {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</label>
        <div class="invalid-tooltip">Please provide a valid {{ Str::ucfirst(Str::replace('_', ' ', $name)) }}</div>
    </div>

</div>


@push('component-html')
    <div class="file-uploader-preview" id="{{ $id ?? $name }}-file-uploader-preview">
    </div>
@endpush
@push('component-script')
    <script>
        let {{ $id ?? $name }}_attached = false;

        let {{ $id ?? $name }}_imageContainer = document.querySelector("#{{ $id ?? $name }}-file-uploader-preview");

        const {{ $id ?? $name }}_followMouse = (event) => {
            {{ $id ?? $name }}_imageContainer.style.left = event.x + "px";
            {{ $id ?? $name }}_imageContainer.style.top = event.y + "px";
        }


        $(document).on('pointerenter', '#{{ $id ?? $name }}', function() {
            if (!{{ $id ?? $name }}_attached) {
                {{ $id ?? $name }}_attached = true;
                const files = this.files[0];

                if (files) {
                    const fileReader = new FileReader();
                    fileReader.readAsDataURL(files);
                    fileReader.addEventListener("load", function() {
                        {{ $id ?? $name }}_imageContainer.style.backgroundImage = "url(" + this.result +
                            ")";
                        {{ $id ?? $name }}_imageContainer.style.display = "block";
                    });
                }

                document.addEventListener("pointermove", {{ $id ?? $name }}_followMouse);
            }
        });
        $(document).on('pointerleave', '#{{ $id ?? $name }}', function() {
            {{ $id ?? $name }}_attached = false;
            {{ $id ?? $name }}_imageContainer.style.display = "";
            document.removeEventListener("pointermove", {{ $id ?? $name }}_followMouse);
        });
    </script>
@endpush


@pushonce('component-style')
    <style>
        .file-uploader-preview {
            position: absolute;
            z-index: 99999;
            background-size: cover;
            border-radius: 5px;
            display: none;
            width: 200px;
            height: 200px;
            background-color: red;
            pointer-events: none;
        }
    </style>
@endpushonce
