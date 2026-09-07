<div class="container image-priviewer">
    <div id="{{ $id }}" class="row text-center">
        @if (!empty($preview))
            @foreach ($preview as $p)
                <div class="{{ $class }}  imgUp">
                    <div class="imagePreview"
                        style="background:url('{{ asset($p->image) ?? (asset($p['image']) ?? asset($p)) }}');background-size: cover;background-repeat: no-repeat;">
                    </div>
                    <label class="btn btn-primary">
                        Upload
                        <input type="hidden" value="{{ $p->id ?? $p['id'] }}" name="{{ $name }}_id[]">
                        <input type="file" value="{{ $p->image }}"
                            name="{{ $name }}[{{ $p->id ?? $p['id'] }}]" class="uploadFile img" accept="image/*"
                            style="width: 0px;height: 0px;overflow: hidden;">
                    </label>
                    <span class="del-img" data-callback="deleteElement" data-delete="{{ route($route, $p->id) }}">
                        <i class="fa-regular fa-circle-xmark"></i>
                    </span>
                </div>
            @endforeach
        @else
            <div class="{{ $class }}  imgUp">
                <div class="imagePreview"></div>
                <label class="btn btn-primary">
                    Upload
                    <input type="file" class="uploadFile img" name="{{ $name }}[]" accept="image/*"
                        value="Upload Photo" style="width: 0px;height: 0px;overflow: hidden;">
                </label>
            </div>
        @endif
        @if (!$onlyPreview)
            <button type="button"
                class="btn btn-icon btn-icon rounded-circle btn-flat-primary btn-sm waves-effect imgAdd">
                <svg aria-hidden="true" focusable="false" data-prefix="fa-duotone" data-icon="plus"
                    class="svg-inline--fa fa-plus fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 448 512">
                    <defs>
                        <style>
                            .fa-secondary {
                                opacity: .4
                            }
                        </style>
                    </defs>
                    <g class="fa-group">
                        <path class="fa-secondary"
                            d="M432 256c0 17.69-14.33 32.01-32 32.01H256v144c0 17.69-14.33 31.99-32 31.99s-32-14.3-32-31.99v-144H48c-17.67 0-32-14.32-32-32.01s14.33-31.99 32-31.99H192v-144c0-17.69 14.33-32.01 32-32.01s32 14.32 32 32.01v144h144C417.7 224 432 238.3 432 256z"
                            fill="currentColor" />
                    </g>
                </svg>
            </button>
        @endif

    </div>
</div>





@pushonce('component-style')
    <style>
        .image-priviewer .btn-primary {
            display: block;
            border-radius: 0px;
            box-shadow: 0px 4px 6px 2px rgba(0, 0, 0, 0.2);
            margin-top: -5px;
        }

        .imgAdd {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            color: #000;
            box-shadow: 1px 1px 10px rgb(0 0 0 / 24%) !important;
            font-size: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .dark-layout .imgAdd {
            color: #fff;
        }

        .del-img {
            position: absolute;
            top: 0px;
            right: 15px;
            width: 30px;
            height: 30px;
            text-align: center;
            line-height: 30px;
            background-color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }

        .del-img svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endpushonce

@pushonce('component-script')
    <script>
        $(".imgAdd").click(function() {

            $(this)
                .closest(".row")
                .find(".imgAdd")
                .before(
                    '<div class=" {{ $class }} imgUp"><div class="imagePreview"></div><label class="btn btn-primary">Upload<input type="file" accept="image/*" name="{{ $name }}[]" class="uploadFile img" value="Upload Photo" style="width:0px;height:0px;overflow:hidden;"></label><span class="del"><svg aria-hidden="true" focusable="false" data-prefix="fa-regular" data-icon="xmark" class="svg-inline--fa fa-xmark fa-w-10" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M312.1 375c9.369 9.369 9.369 24.57 0 33.94s-24.57 9.369-33.94 0L160 289.9l-119 119c-9.369 9.369-24.57 9.369-33.94 0s-9.369-24.57 0-33.94L126.1 256L7.027 136.1c-9.369-9.369-9.369-24.57 0-33.94s24.57-9.369 33.94 0L160 222.1l119-119c9.369-9.369 24.57-9.369 33.94 0s9.369 24.57 0 33.94L193.9 256L312.1 375z" fill="currentColor"/></svg></span></div>'
                );
        });
        $(document).on("click", "span.del", function() {
            $(this).parent().remove();
            $(".imgAdd").show();
        });
        $(function() {
            $(document).on("change", ".uploadFile", function() {
                var uploadFile = $(this);
                var files = !!this.files ? this.files : [];
                if (!files.length || !window.FileReader)
                    return;

                if (/^image/.test(files[0].type)) {

                    var reader = new FileReader();
                    reader.readAsDataURL(files[0]);

                    reader.onloadend = function() {

                        uploadFile
                            .closest(".imgUp")
                            .find(".imagePreview")
                            .css("background-image", "url(" + this.result + ")")
                    };
                }
            });
        });

        function deleteElement(el) {
            $(el).parent('.imgUp').remove()
        }
    </script>
@endpushonce
