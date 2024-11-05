@php
    use App\Utils\Tanggal;
@endphp

@extends('admin.layouts.base')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Edit Usaha {{ $usaha->nama_usaha }}</h5>
        </div>
        <div class="card-body">
            <form action="/db/app/{{ $usaha->id }}/edit" method="post" id="FormEditUsaha">
                @csrf

                <input type="hidden" name="_provinsi" id="_provinsi" value="{{ $kode_wilayah[0] }}">
                <input type="hidden" name="_kabupaten" id="_kabupaten"
                    value="{{ $kode_wilayah[0] }}.{{ $kode_wilayah[1] }}">
                <input type="hidden" name="_kecamatan" id="_kecamatan"
                    value="{{ $kode_wilayah[0] }}.{{ $kode_wilayah[1] }}.{{ $kode_wilayah[2] }}">
                <input type="hidden" name="_desa" id="_desa"
                    value="{{ $kode_wilayah[0] }}.{{ $kode_wilayah[1] }}.{{ $kode_wilayah[2] }}.{{ $kode_wilayah[3] }}">

                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="provinsi">Provinsi</label>
                            <select class="form-control select2" name="provinsi" id="provinsi">
                                <option value="">-- Pilih Provinsi --</option>
                                @foreach ($wilayah as $w)
                                    <option value="{{ $w->kode }}"
                                        {{ $w->kode == $kode_wilayah[0] ? 'selected' : '' }}>
                                        {{ $w->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_provinsi"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="kabupaten">Kabupaten</label>
                            <select class="form-control select2" name="kabupaten" id="kabupaten">
                                <option value="">-- Pilih kabupaten --</option>
                            </select>
                            <small class="text-danger" id="msg_kabupaten"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="kecamatan">Kecamatan</label>
                            <select class="form-control select2" name="kecamatan" id="kecamatan">
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <small class="text-danger" id="msg_kecamatan"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="desa">Desa</label>
                            <select class="form-control select2" name="desa" id="desa">
                                <option value="">-- Pilih Desa --</option>
                            </select>
                            <small class="text-danger" id="msg_desa"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="nama_usaha">Nama Usaha</label>
                            <input autocomplete="off" type="text" name="nama_usaha" id="nama_usaha"
                                class="form-control form-control-sm" value="{{ $usaha->nama_usaha }}">
                            <small class="text-danger" id="msg_nama_usaha"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="domain">Domain</label>
                            <input autocomplete="off" type="url" name="domain" id="domain"
                                class="form-control form-control-sm" value="{{ $usaha->domain }}">
                            <small class="text-danger" id="msg_domain"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="domain_alternatif">Domain Alternatif</label>
                            <input autocomplete="off" type="url" name="domain_alternatif" id="domain_alternatif"
                                class="form-control form-control-sm" value="{{ $usaha->domain_alt }}">
                            <small class="text-danger" id="msg_domain_alternatif"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="tagihan_invoice">Tagihan Invoice</label>
                            <select class="form-control select2" name="tagihan_invoice" id="tagihan_invoice">
                                <option value="">-- Tagihan Invoice --</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}"
                                        {{ $i == $usaha->tagihan_invoice ? 'selected' : '' }}>
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-danger" id="msg_tagihan_invoice"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="biaya_maintenance">Biaya Maintenance</label>
                            <input autocomplete="off" type="text" name="biaya_maintenance" id="biaya_maintenance"
                                class="form-control form-control-sm" value="{{ number_format($usaha->biaya, 2) }}">
                            <small class="text-danger" id="msg_biaya_maintenance"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="tgl_register">Tgl Register</label>
                            <input autocomplete="off" type="text" name="tgl_register" id="tgl_register"
                                class="form-control form-control-sm date"
                                value="{{ Tanggal::tglIndo($usaha->tgl_register) }}">
                            <small class="text-danger" id="msg_tgl_register"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="tgl_pakai">Tgl Pakai</label>
                            <input autocomplete="off" type="text" name="tgl_pakai" id="tgl_pakai"
                                class="form-control form-control-sm date"
                                value="{{ Tanggal::tglIndo($usaha->tgl_pakai) }}">
                            <small class="text-danger" id="msg_tgl_pakai"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="masa_aktif">Masa Aktif</label>
                            <input autocomplete="off" type="text" name="masa_aktif" id="masa_aktif"
                                class="form-control form-control-sm date"
                                value="{{ Tanggal::tglIndo($usaha->masa_aktif) }}">
                            <small class="text-danger" id="msg_masa_aktif"></small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" id="SimpanUsaha" class="btn btn-sm btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        $("#biaya_maintenance").maskMoney({
            allowNegative: true
        });

        function prov() {
            var prov = $('#provinsi').val()

            $.ajax({
                type: 'GET',
                url: '/db/app/kabupaten/' + prov,
                data: {
                    prov
                },
                success: function(result) {
                    selectOption(result.data, '#kabupaten', 'Pilih Kabupaten', $('#_kabupaten').val())
                }
            })
        }

        $(document).on('change', '#provinsi', function(e) {
            prov()
        })

        $(document).on('change', '#kabupaten', function(e) {
            var kab = $(this).val()

            $.ajax({
                type: 'GET',
                url: '/db/app/kecamatan/' + kab,
                data: {
                    kab
                },
                success: function(result) {
                    selectOption(result.data, '#kecamatan', 'Pilih Kecamatan', $('#_kecamatan').val())
                }
            })
        })

        $(document).on('change', '#kecamatan', function(e) {
            var kab = $(this).val()

            $.ajax({
                type: 'GET',
                url: '/db/app/desa/' + kab,
                data: {
                    kab
                },
                success: function(result) {
                    selectOption(result.data, '#desa', 'Pilih Desa', $('#_desa').val())
                }
            })
        })

        $(document).on('click', '#SimpanUsaha', function(e) {
            e.preventDefault();

            $('small').html('')
            var form = $('#FormEditUsaha')
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire('Berhasil', result.msg, 'success').then(() => {
                            window.location.href = '/db/app/'
                        })
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

        function selectOption(data, target, title, selected = '') {
            var select, option;
            select = $(target)

            $(target).html('')
            option = $('<option></option>').attr('value', '').text('-- ' + title + ' --')
            select.append(option)

            data.map((item) => {
                option = $('<option></option>').attr('value', item.kode).text(item.nama)
                select.append(option)
            })

            $(target).val(selected).change()
        }

        prov()
    </script>
@endsection
