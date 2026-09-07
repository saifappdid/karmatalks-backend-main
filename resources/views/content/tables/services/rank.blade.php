@extends('layouts/contentLayoutMaster')

@section('title', 'Rank services')
@section('page-style')
@endsection

@section('content')

    <section>
        <div class="row match-height">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <x-card title="Service Ranking">
                    <table id="table" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30px">#</th>
                                <th>Rank</th>
                                <th>Title</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody id="tablecontents">
                            @foreach ($services as $service)
                                <tr class="row1" data-id="{{ $service->id }}">
                                    <td class="" draggable=""><i data-feather="move"></i></td>
                                    <td>{{ $service->rank }}</td>
                                    <td>{{ $service->title }}</td>
                                    <td><span class="badge badge-light-primary">
                                            {{ date('d-m-Y h:m:s', strtotime($service->created_at)) }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-card>
            </div>
        </div>
    </section>
@endsection

@section('vendor-script')
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notiflix@3.2.5/dist/notiflix-aio-3.2.5.min.js"></script>
@endsection

@section('page-script')
    <script>
        $(function() {

            $("#tablecontents").sortable({
                items: "tr",
                cursor: 'move',
                opacity: 0.6,
                update: function() {
                    rankSections();
                }
            });

            function rankSections() {
                var services = [];
                var token = $('meta[name="csrf-token"]').attr('content');
                $('tr.row1').each(function(index, element) {
                    services.push({
                        id: $(this).attr('data-id'),
                        position: index + 1
                    });
                });
                // Notiflix.Loading.standard('Updating Position...');
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('admin.services.post-rank') }}",
                    data: {
                        services: services,
                        _token: token
                    },
                    success: function(response) {
                        console.log(response);
                        if (response) {
                            Notiflix.Notify.success(response.message);
                            setTimeout(function() {
                                location.reload();
                                // location.replace(response.route);
                            }, 2000);
                        } else {
                            Notiflix.Notify.failure('Something went wrong');
                        }
                    }
                });
            }
        });
    </script>
@endsection
