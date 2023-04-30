@extends('layouts.admin')

@section('title', 'Category')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">List @yield('title')</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3 text-end">
                    <button class="btn btn-primary" onclick="add()"><i class="mdi mdi-plus"></i> Add Data</button>
                </div>
                <div class="col-md-12">
                    {{ $dataTable->table() }}
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_form">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="form_data">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <div class="modal-title">Tambah Data</div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name">Nama : </label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Masukkan Nama...">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-2 d-none" id="edit_image">
                            <label>Last Image : </label><br>
                            <img id="last_image" src="" alt="" style="height: 100px">
                        </div>
                        <div class="mb-3">
                            <label for="image">Gambar : </label>
                            <input type="file" class="form-control" id="image" name="image">
                            <div class="invalid-feedback"></div>
                            <div id="warning" class="d-none text-danger">*) Dont Upload if not change</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn_submit">Submit</button>
                    </div>
                </form>
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
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}

    <script>
        let id_use;

        function add() {
            $("#id").val(null);
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').empty();
            $("#edit_image").addClass('d-none');
            $("#warning").addClass('d-none');
            $('#form_data')[0].reset();
            $('.modal-title').text('Tambah Data')
            $("#modal_form").modal('show');
        };

        function edit(id) {
            $("#id").val(id);
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').empty();
            $('#form_data')[0].reset();
            $('.modal-title').text('Edit Data');
            $.ajax({
                url: "{{ route('category.edit') }}",
                type: "POST",
                data: {
                    id: id
                },
                dataType: "JSON",
                success: function(data) {
                    for (const [key, value] of Object.entries(data)) {
                        $(`[name=${key}]`).val(value);
                    }
                    let path = `{{ asset('storage/category/${data.filename}') }}`

                    $("#edit_image").removeClass('d-none');
                    $("#warning").removeClass('d-none');
                    $("#last_image").attr('src', path);
                    $("#modal_form").modal('show');
                }
            })
        };

        $('.btn_submit').on('click', function(e) {
            e.preventDefault();
            let formData = new FormData($('#form_data')[0]);
            $.ajax({
                url: "{{ route('category.store') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                success: function(data) {
                    Swal.fire({
                        icon: "success",
                        title: "Success",
                        text: data.text,
                    })
                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');
                    $('#form_data')[0].reset();
                    $("#modal_form").modal("hide");
                    window.LaravelDataTables["categories-table"].ajax.reload();
                },
                error: function(res) {
                    let errors = res.responseJSON?.errors

                    $('.invalid-feedback').empty();
                    $('.form-control').removeClass('is-invalid');

                    if (errors) {
                        for (const [key, value] of Object.entries(errors)) {
                            $(`[name=${key}]`).addClass("is-invalid");
                            $(`[name=${key}]`).next().html(value);
                        }
                    }
                }
            })
        })

        function destroy(id) {
            Swal.fire({
                title: "Apakah kamu yakin?",
                text: "Data akan terhapus!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('category.destroy') }}",
                        type: "POST",
                        data: {
                            id: id
                        },
                        dataType: "JSON",
                        success: function(data) {
                            Swal.fire({
                                icon: "success",
                                title: "Success",
                                text: data.text,
                            })
                            window.LaravelDataTables["categories-table"].ajax.reload();
                        }
                    })
                }
            })
        };

        $(document).on('click', '[data-bs-toggle="modal"]', function(e) {
            var nameLabel = $(this).data('name');
            var imageSrc = $(this).data('img');
            $("#modalImage").attr("src", imageSrc);
            $("#imageModalLabel").text(nameLabel);
        });
    </script>
@endpush
