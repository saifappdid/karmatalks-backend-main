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
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card>
                    <livewire:product-table />
                </x-card>
            </div>
        </div>
    </section>


    <x-side-modal title="Add product" id="add-blade-modal" modalWidth="w-75">
        <x-form id="add-product" method="POST" class="" :route="route('admin.products.store')">
            <div class="col-md-6 col-12 ">
                <x-select :required="false" name="type" class="type" :options="['product', 'service', 'healing']" />
            </div>
            <div class="col-md-6 col-12 ">
                <x-select name="category_id" :options="[]" />
            </div>
            <div class="col-md-3">
                <x-input name="title" />
            </div>
            <div class="col-md-3 price-div d-none">
                <x-input :required="false" name="price" />
            </div>
            <div class="col-md-3 price-dollar-div d-none">
                <x-input :required="false" name="price_dollar" />
            </div>
            <div class="col-md-3">
                <x-input-file name="image" />
            </div>
            <div class="col-md-12">
                <x-editor label="Description" name="description" />
            </div>
            <x-divider text="Add Variations" />
            <div class="col-md-12 variation-div">
                <div id="variants-container" class="variants-container">
                    <!-- Dynamic variant rows will be added here -->

                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-variant-btn">Add Variation</button>
            </div>
            <x-divider text="Other Images" />
            <div class="col-md-12">
                <x-image-uploader name="images" />
            </div>


        </x-form>
    </x-side-modal>
    {{-- <x-side-modal title="Update product" id="edit-product-modal" modalWidth="w-75">
        <x-form id="edit-product" method="POST" class="" :route="route('admin.products.update')">

            <div class="col-md-6 col-12 ">
                <x-select :required="false" name="type" class="type" :options="['product', 'service']" />
            </div>
            <div class="col-md-6 col-12 ">
                <x-select name="category_id" :options="[]" />
            </div>
            <div class="col-md-6">
                <x-input name="title" />
                <x-input name="id" type="hidden" />
            </div>
            <div class="col-md-6 price-div d-none">
                <x-input :required="false" name="price" class="price" />
            </div>
            <div class="col-md-10 col-10 ">
                <x-input-file name="image" />
            </div>
            <div class="col-md-2 col-2 mt-2">
                <div class="avatar avatar-lg">
                    <img class="view-on-click image-edit" id="image-preview" src="" alt="avatar">
                </div>
            </div>
            <div class="col-md-12">
                <x-editor id="edit_description" label="Description" name="description" />
            </div>
            <x-divider text="Add Variations" />
            <div class="col-md-12">
                <div id="variants-container" class="variants-container">
                    <!-- Dynamic variant rows will be added here -->

                </div>
                <button type="button" class="btn btn-sm btn-primary mt-2" id="add-variant-btn">Add Variation</button>
            </div>

            <x-divider text="Other Images" />
            <div class="col-md-12">
                <x-image-uploader name="images" />
            </div>



        </x-form>
    </x-side-modal> --}}

    <x-modal id="product-description" :footer="false" />
@endsection
@section('page-script')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.livewire.on('mailSent', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Email Sent!',
                    text: 'The export file has been sent successfully.',
                    confirmButtonText: 'OK'
                });
                location.reload();
            });
        });

        $(document).ready(function() {
            let variantIndex = 0;
            $('#add-variant-btn').on('click', function() {
                variantIndex++;
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
        $(document).on('change', '.type', function() {
            const type = $(this).val();
            const form = $(this).closest('form');
            const categorySelect = form.find('select[name="category_id"]');

            togglePriceDiv(form);

            if (type === 'product') {
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
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr);
                    }
                });
            }
        });
        $(document).ready(function() {
            $(document).on('click', '[data-show]', function() {
                const modal = $(this).data('show');
                $(`#${modal}`).modal('show');
            });
        });

        function addDefaultVariant(container) {
            let variantIndex = container.find('.variant-row').length;
            let html = `
          
                <div class="row variant-row mb-2">
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][title]" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][mrp_price]" class="form-control" placeholder="MRP Price" required>
                    </div>
                     <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][price]" class="form-control" placeholder="Selling Price" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][mrp_price_dollar]" class="form-control" placeholder="MRP Price Dollar" required>
                    </div>
                     <div class="col-md-2">
                        <input type="text" name="variants[${variantIndex}][price_dollar]" class="form-control" placeholder="Selling Price Dollar" required>
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

        function setValue(data, modal) {
            const $modal = $(modal);
            $(`${modal} #id`).val(data.id);
            $modal.find('select[name="type"]').val(data.type).trigger(
                'change');
            $modal.find('select[name="category_id"]').val(data.category_id);
            $modal.find('input[name="title"]').val(data.title);
            $modal.find('input[name="price"]').val(data.price);
            // $modal.find('input[name="image"]').val(
            //     '');
            $modal.find('#image-preview').attr('src', data.image);
            fullEditor_edit_description.root.innerHTML = data.description;
            // $modal.find('textarea[name="description"]').val(data.description);


            setTimeout(() => {
                $modal.find('select[name="category_id"]').val(data.category_id).trigger('change');
            }, 300);

            const container = $modal.find('.variants-container');
            container.empty();



            console.log(data.variants);


            if (data.variants && data.variants.length > 0) {
                console.log('has variants');

                data.variants.forEach((variant, index) => {
                    console.log(variant, index);

                    let html = `
                <div class="row variant-row mb-2">
                    <div class="col-md-2">
                        <input type="text" name="variants[${index}][title]" class="form-control" placeholder="Title" value="${variant.title}">
                    </div>
                      <div class="col-md-2">
                        <input type="text" name="variants[${index}][mrp_price]" class="form-control" placeholder="MRP Price" value="${variant.mrp_price}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${index}][price]" class="form-control" placeholder="Selling Price" value="${variant.price}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="variants[${index}][mrp_price_dollar]" class="form-control" placeholder="MRP Price Dollar" value="${variant.mrp_price_dollar}">
                    </div>
                     <div class="col-md-2">
                        <input type="text" name="variants[${index}][price_dollar]" class="form-control" placeholder="Selling Price Dollar" value="${variant.price_dollar}">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-variant-btn">Remove</button>
                    </div>
                </div>
                `;
                    console.log(html);
                    container.append(html);
                });
                container.show();
            } else {
                container.hide();
            }
            console.log(data);


            // Images



            // Show modal
            $modal.modal('show');
        }

        $(document).on('click', '[data-description]', function() {
            const data = $(this).closest('tr').find('[data-hidden]').html();
            const modal = $('#product-description');
            $(modal).find('.modal-body').html(data);
            $(modal).find('.modal-title').text('Description');
            $(modal).modal('show');
        });
    </script>
@endsection
