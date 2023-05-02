@extends('layouts.admin')

@section('title', 'Product')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">List @yield('title')</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="imageModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="margin: auto">
                    <img src="" id="modalImage" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="descriptionModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionLabel">Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="descriptionInModal"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}

    <script>
        $(document).on('click', '#imageTrigger', function(e) {
            let nameLabel = $(this).data('name');
            let imageSrc = $(this).data('img');
            $("#modalImage").attr("src", imageSrc);
            $("#imageModalLabel").text(nameLabel);
        });

        $(document).on('click', '#descTrigger', function(e) {
            let nameLabel = $(this).data('name');
            let description = $(this).data('content');
            $('#descriptionInModal').text(description);
            $("#descriptionLabel").text(nameLabel);
        });
    </script>
@endpush
