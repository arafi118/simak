@php
    $identitas_lembaga = 'active show';
    $pengelola_bumdes = '';
    $upload_logo = '';

    if (!in_array('personalisasi_sop.identitas_lembaga', Session::get('tombol'))) {
        $identitas_lembaga = '';
    }

    if (
        in_array('personalisasi_sop.sebutan_pengelola', Session::get('tombol')) &&
        ($identitas_lembaga == '' && $upload_logo == '')
    ) {
        $pengelola_bumdes = 'active show';
    }

    if (
        in_array('personalisasi_sop.logo', Session::get('tombol')) &&
        ($identitas_lembaga == '' && $pengelola_bumdes == '')
    ) {
        $upload_logo = 'active show';
    }
@endphp

@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-10 col-12  order-2">
            <div class="tab-content" id="v-pills-tabContent">
                <div class="tab-pane fade {{ $identitas_lembaga }}" id="lembaga" role="tabpanel"
                    aria-labelledby="lembaga-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Identitas Lembaga</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('sop.partials._lembaga')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $pengelola_bumdes }}" id="pengelola" role="tabpanel"
                    aria-labelledby="pengelola-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Sebutan Pengelola Bumdes</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('sop.partials._pengelola')
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $upload_logo }}" id="upload-logo" role="tabpanel"
                    aria-labelledby="upload-logo-tab">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Upload Logo</h5>
                        </div>
                        <div class="card-body pt-0">
                            @include('sop.partials._logo')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-12 order-1">
            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                @if (in_array('personalisasi_sop.identitas_lembaga', Session::get('tombol')))
                    <a class="nav-link {{ $identitas_lembaga }}" id="lembaga-tab" data-toggle="pill" href="#lembaga"
                        role="tab" aria-controls="lembaga" aria-selected="true">Identitas Lembaga</a>
                @endif
                @if (in_array('personalisasi_sop.sebutan_pengelola', Session::get('tombol')))
                    <a class="nav-link {{ $pengelola_bumdes }}" id="pengelola-tab" data-toggle="pill" href="#pengelola"
                        role="tab" aria-controls="pengelola" aria-selected="false">Sebutan Pengelola</a>
                @endif
                @if (in_array('personalisasi_sop.logo', Session::get('tombol')))
                    <a class="nav-link {{ $upload_logo }}" id="upload-logo-tab" data-toggle="pill" href="#upload-logo"
                        role="tab" aria-controls="logo" aria-selected="false">Logo</a>
                @endif
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

            $('#logo').trigger('click')
        })

        $(document).on('change', '#logo', function(e) {
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
                                $(".brand-logo").attr("src", reader.result);
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
