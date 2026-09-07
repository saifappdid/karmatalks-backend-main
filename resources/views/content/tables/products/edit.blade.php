@extends('layouts/contentLayoutMaster')

@section('title', 'Product')
@section('page-style')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .dropdown-menu {
            transform: scale(1) !important;
        }
    </style>
@endsection

@section('content')

    <section>
        <x-card title="Edit Product">
            <x-form successCallback="redirect" id="edit-product" method="POST" class="" :route="route('admin.products.update')">

                <div class="col-md-6 col-12 ">
                    <x-select :required="false" name="type" class="type" :options="['product', 'service', 'healing']" />
                </div>
                <div class="col-md-6 col-12 ">
                    <x-select name="category_id" :options="[]" />
                </div>
                <div class="col-md-3">
                    <x-input name="title" value="{{ $data->title }}" />
                    <x-input name="id" type="hidden" value="{{ $data->id }}" />
                </div>
                <div class="col-md-3 price-div d-none">
                    <x-input :required="false" name="price" class="price" value="{{ $data->price }}" />
                </div>
                <div class="col-md-3 price-dollar-div d-none">
                    <x-input :required="false" name="price_dollar" class="price" value="{{ $data->price_dollar }}" />
                </div>
                <div class="col-md-2 col-10 ">
                    <x-input-file name="image" />
                </div>
                <div class="col-md-1 col-2 mt-2">
                    <div class="avatar avatar-lg">
                        <img class="view-on-click image-edit" id="image-preview" src="{{ $data->image }}" alt="avatar">
                    </div>
                </div>
                <div class="col-md-12">
                    <x-editor id="edit_description" label="Description" name="description" />
                </div>
                @if ($data->type === 'product')
                    <x-divider text="Add Variations" />
                    <div class="col-md-12 variation-div">
                        <div id="variants-container" class="variants-container">
                            <!-- Dynamic variant rows will be added here -->
                            @if ($data->variants && $data->variants->count() > 0)
                                @foreach ($data->variants as $index => $variant)
                                    <div class="row variant-row mb-2">
                                        <div class="col-md-2">
                                            <input type="text" name="variants[{{ $index }}][title]"
                                                class="form-control" placeholder="Title" value="{{ $variant->title }}"
                                                required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="variants[{{ $index }}][mrp_price]"
                                                class="form-control" placeholder="MRP Price in INR"
                                                value="{{ $variant->mrp_price }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="variants[{{ $index }}][price]"
                                                class="form-control" placeholder="Selling Price in INR"
                                                value="{{ $variant->price }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="variants[{{ $index }}][mrp_price_dollar]"
                                                class="form-control" placeholder="MRP Price in Dollar"
                                                value="{{ $variant->mrp_price_dollar }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="variants[{{ $index }}][price_dollar]"
                                                class="form-control" placeholder="Selling Price in Dollar"
                                                value="{{ $variant->price_dollar }}" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button"
                                                class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <button type="button" class="btn btn-sm btn-primary mt-2" id="add-variant-btn">Add
                            Variation</button>
                    </div>
                @endif

                <x-divider text="Other Images" />
                <div class="col-md-12">
                    <x-image-uploader name="images" :preview="$data->images" :route="'admin.products.delete-image'" />
                </div>
            </x-form>
        </x-card>
    </section>
@endsection
@section('page-script')

    <script>
        function redirect() {
            location.href = "{{ route('admin.products.index') }}"
        }
        $(document).ready(function() {

            $('#add-variant-btn').on('click', function() {
                let variantIndex = $('.variants-container .variant-row').length;

                let html = `
        <div class="row variant-row mb-2">
            <div class="col-md-2">
                <input type="text" name="variants[${variantIndex}][title]" class="form-control" placeholder="Title">
            </div>
            <div class="col-md-2">
                <input type="text" name="variants[${variantIndex}][mrp_price]" class="form-control" placeholder="MRP Price in INR">
            </div>
            <div class="col-md-2">
                <input type="text" name="variants[${variantIndex}][price]" class="form-control" placeholder="Selling Price in INR">
            </div>
            <div class="col-md-2">
                <input type="text" name="variants[${variantIndex}][mrp_price_dollar]" class="form-control" placeholder="MRP Price in Dollar">
            </div>
            <div class="col-md-2">
                <input type="text" name="variants[${variantIndex}][price_dollar]" class="form-control" placeholder="Selling Price in Dollar">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
            </div>
        </div>
    `;

                $('.variants-container').append(html);
            });


            // Remove variant row
            $(document).on('click', '.remove-variant-btn', function() {
                $(this).closest('.variant-row').remove();
            });
        });
        const data = @json($data);
        setTimeout(() => {
            $('.type').val(data.type).trigger('change');
        }, 300);
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                $(`#${modal}`).modal('show');
            });

            $(document).on('change', '.type', function() {
                const type = $(this).val();
                const form = $(this).closest('form');
                const categorySelect = form.find('select[name="category_id"]');
                console.log('sdfsf');
                var category_id = "{{ $data->category_id }}";

                togglePriceDiv(form);

                if (data.type === 'product') {
                    let container = form.find('.variants-container');
                    if (container.find('.variant-row').length === 0) {
                        addDefaultVariant(container);
                    }
                    $('.variation-div').attr('hidden', false);
                } else {
                    $('.variation-div').attr('hidden', true);
                }


                categorySelect.html('<option value="" disabled selected>Select Category</option>');

                if (type) {
                    $.ajax({
                        url: "{{ route('admin.products.get-categories') }}",
                        type: 'GET',
                        data: {
                            type: type
                        },
                        success: function(response) {
                            if (response && response.length) {
                                response.forEach(function(item) {
                                    categorySelect.append(
                                        `<option value="${item.id}">${item.title}</option>`
                                    );
                                });

                                if (category_id) {
                                    categorySelect.val(category_id);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error(xhr);
                        }

                    });
                }
            });

            $('.type').trigger('change');




        });

        function addDefaultVariant(container) {
            let variantIndex = container.find('.variant-row').length;
            let html = `
          
                <div class="row variant-row mb-2">
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][title]" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][mrp_price]" class="form-control" placeholder="MRP Price in INR" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][price]" class="form-control" placeholder="Selling Price in INR" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][mrp_price_dollar]" class="form-control" placeholder="MRP Price in Dollar" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][price_dollar]" class="form-control" placeholder="Selling Price in Dollar" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
                    </div>
                </div>
           
            `;
            container.append(html);
        }


        function togglePriceDiv(form) {
            const type = form.find('select[name="type"]').val();
            const priceDiv = form.find('.price-div');
            const priceDollarDiv = form.find('.price-dollar-div');
            if (type == 'service') {
                priceDiv.removeClass('d-none');
                priceDollarDiv.removeClass('d-none');
            } else {
                priceDiv.addClass('d-none');
                priceDollarDiv.addClass('d-none');

            }
        }

        fullEditor_edit_description.root.innerHTML = data.description;
    </script>
@endsection
