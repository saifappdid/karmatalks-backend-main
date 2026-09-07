<div class="col-lg-12 col-12" id="main-container">
    @php
        $duration = [
            (object) ['id' => '15', 'name' => '15 Minutes'],
            (object) ['id' => '30', 'name' => '30 Minutes'],
            (object) ['id' => '60', 'name' => '1 Hour'],
        ];
    @endphp
    <div data-repeater-list="prices">
        @if (count($prices) > 0)
            @forelse ($prices as $price)
                <div class="row container-item" data-index="0">
                    <input type="hidden" name="prices[0][price_id]" value="{{ $price->id ?? '' }}">

                    <div class="col-lg-3 col-12">
                        <label>Services</label>
                        <select name="prices[0][price_services][]" class="select2 form-control">
                            <option value="" disabled>Select Services</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @if ($service->id == $price->service_id) selected @endif>
                                    {{ $service->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-12">
                        <x-select name="prices[0][duration]" :select="$price->duration ?? ''" label="Duration" :options="$duration"
                            placeholder="Select duration" />
                    </div>

                    <div class="col-lg-2 col-12">
                        <x-input name="prices[0][question]" label="Questions" value="{{ $price->questions ?? '' }}"
                            placeholder="Enter questions" :required="false" />
                    </div>

                    <div class="col-lg-2 col-12">
                        <x-input name="prices[0][charge]" label="Charge" value="{{ $price->charge ?? '' }}"
                            placeholder="Enter charge" type="number" />
                    </div>

                    <div class="col-md-2 col-12 d-flex align-items-center">
                        <button type="button" data-option-delete="{{ $price->id }}"
                            class="btn btn-outline-danger delete-item text-nowrap px-1">
                            <i data-feather="x" class="mr-25"></i>
                            <span>Delete</span>
                        </button>
                    </div>
                </div>
            @empty
            @endforelse
            <div class="col-12 text-center mt-2">
                <button id="add-more" type="button" class="btn btn-outline-primary ">
                    Add
                </button>
            </div>
        @else
            <x-repeater name="service_charges" :maxLimit="3" :fields="[
                [
                    'col' => '3',
                    'type' => 'select',
                    'name' => 'services',
                    // 'multiple' => true,
                    'options' => $services,
                ],
                [
                    'col' => '3',
                    'type' => 'select',
                    'name' => 'duration',
                    'options' => $duration,
                ],
                [
                    'col' => '3',
                    'type' => 'number',
                    'name' => 'question',
                    'label' => 'Question',
                ],
                [
                    'col' => '3',
                    'type' => 'number',
                    'name' => 'charge',
                    'label' => 'Charge',
                ],
            ]" />
        @endif
    </div>

</div>
<script src="{{ asset(mix('vendors/js/forms/repeater/jquery.repeater.min.js')) }}"></script>
<script>
    $('.select2').select2();
    $('#add-more').cloneData({
        mainContainerId: 'main-container',
        cloneContainer: 'container-item',
        removeButtonClass: 'remove-item',
        removeConfirm: true,
        removeConfirmMessage: 'Are you sure want to delete?',
        append: '',
        minLimit: 0,
        defaultRender: 1,
        afterRender: function(newElem) {
            $(newElem).find('.select2').select2();
            if (feather) feather.replace();
            $('#main-container .container-item').each(function(i) {
                $(this).attr('data-index', i);
                $(this).find('input, select, textarea').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        name = name.replace(/prices\[\d+\]/, `prices[${i}]`);
                        $(this).attr('name', name);
                    }
                });
            });
        }
    });
    $('#add-more').on('click', function() {
        const newRow = $(this).parents('#main-container').find('.container-item').last();
        newRow.find('.delete-item').attr("data-option-delete", "");
    })
    $(document).on('click', '[data-option-delete]', function() {
        const optId = $(this).data('option-delete');
        if (confirm('Are you sure want to delete ?')) {
            console.log(optId);

            $(this).closest('.container-item').remove();
            if (optId) {
                deleteItem(optId);
            }
        };
    });

    function deleteItem(id) {
        Notiflix.Loading.standard('Updating detail...');
        $.ajax({
            method: "POST",
            url: "{{ route('admin.astros.delete-price') }}",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
            },
            success: function(response) {
                Notiflix.Loading.remove();
                console.log(response);
                Notiflix.Notify.success(response.message);
                setTimeout(() => {
                    location.reload();
                }, 3000);
            },
            error: function(response) {
                Notiflix.Loading.remove();
                Notiflix.Notify.failure('Something went wrong!');
                console.log(response);
            }
        });
    }
</script>
