@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-10 col-12  order-2">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade active show" id="lembaga" role="tabpanel" aria-labelledby="lembaga-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Identitas Lembaga</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('sop.partials._lembaga')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pengelola" role="tabpanel" aria-labelledby="pengelola-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Sebutan Pengelola Bumdes</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('sop.partials._pengelola')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-12 order-1">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                <a class="nav-link active show" id="lembaga-tab" data-toggle="pill" href="#lembaga" role="tab"
                    aria-controls="lembaga" aria-selected="true">Identitas Lembaga</a>
                <a class="nav-link" id="pengelola-tab" data-toggle="pill" href="#pengelola" role="tab"
                    aria-controls="pengelola" aria-selected="false">Sebutan Pengelola</a>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        var tahun = "{{ date('Y') }}"
        var bulan = "{{ date('m') }}"

        $(".money").maskMoney();

        $(document).on('click', '.btn-simpan', async function(e) {
            e.preventDefault()

            if ($(this).attr('id') == 'SimpanSPK') {
                await $('#spk').val(quill.container.firstChild.innerHTML)
            }

            if ($(this).attr('id') == 'SimpanBeritaAcara') {
                await $('#ba').val(quill1.container.firstChild.innerHTML)
            }

            var form = $($(this).attr('data-target'))
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Toastr('success', result.msg)

                        if (result.nama_lembaga) {
                            $('#nama_lembaga_sort').html(result.nama_lembaga)
                        }
                    }
                },
                error: function(result) {
                    const respons = result.responseJSON;

                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error')
                    $.map(respons, function(res, key) {
                        $('#' + key).parent('.input-group.input-group-static').addClass(
                            'is-invalid')
                        $('#msg_' + key).html(res)
                    })
                }
            })
        })

        $(document).on('click', '#EditLogo', function(e) {
            e.preventDefault()

            $('#logo_kec').trigger('click')
        })

        $(document).on('change', '#logo_kec', function(e) {
            e.preventDefault()

            var logo = $(this).get(0).files[0]
            if (logo) {
                var form = $('#FormLogo')
                var formData = new FormData(document.querySelector('#FormLogo'));
                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        if (result.success) {
                            var reader = new FileReader();

                            reader.onload = function() {
                                $("#previewLogo").attr("src", reader.result);
                                $(".colored-shadow").css('background-image',
                                    "url(" + reader.result + ")")
                            }

                            reader.readAsDataURL(logo);
                            Toastr('success', result.msg)
                        } else {
                            Toastr('error', result.msg)
                        }
                    }
                })
            }
        })
    </script>
@endsection
