@php
    $thn_awal = explode('-', $usaha->tgl_pakai)[0];
@endphp

@extends('layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">

            <form action="/pelaporan/preview" method="post" id="FormPelaporan" target="_blank">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="tahun">Tahunan</label>
                            <select class="form-control select2" name="tahun" id="tahun">
                                <option value="">---</option>
                                @for ($i = $thn_awal; $i <= date('Y'); $i++)
                                    <option {{ $i == date('Y') ? 'selected' : '' }} value="{{ $i }}">
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            <small class="text-danger" id="msg_tahun"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="bulan">Bulanan</label>
                            <select class="form-control select2" name="bulan" id="bulan">
                                <option value="">---</option>
                                <option {{ date('m') == '01' ? 'selected' : '' }} value="01">01. JANUARI</option>
                                <option {{ date('m') == '02' ? 'selected' : '' }} value="02">02. FEBRUARI</option>
                                <option {{ date('m') == '03' ? 'selected' : '' }} value="03">03. MARET</option>
                                <option {{ date('m') == '04' ? 'selected' : '' }} value="04">04. APRIL</option>
                                <option {{ date('m') == '05' ? 'selected' : '' }} value="05">05. MEI</option>
                                <option {{ date('m') == '06' ? 'selected' : '' }} value="06">06. JUNI</option>
                                <option {{ date('m') == '07' ? 'selected' : '' }} value="07">07. JULI</option>
                                <option {{ date('m') == '08' ? 'selected' : '' }} value="08">08. AGUSTUS</option>
                                <option {{ date('m') == '09' ? 'selected' : '' }} value="09">09. SEPTEMBER</option>
                                <option {{ date('m') == '10' ? 'selected' : '' }} value="10">10. OKTOBER</option>
                                <option {{ date('m') == '11' ? 'selected' : '' }} value="11">11. NOVEMBER</option>
                                <option {{ date('m') == '12' ? 'selected' : '' }} value="12">12. DESEMBER</option>
                            </select>
                            <small class="text-danger" id="msg_bulan"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label" for="hari">Harian</label>
                            <select class="form-control select2" name="hari" id="hari">
                                <option value="">---</option>
                                @for ($j = 1; $j <= 31; $j++)
                                    @if ($j < 10)
                                        <option value="0{{ $j }}">0{{ $j }}</option>
                                    @else
                                        <option value="{{ $j }}">{{ $j }}</option>
                                    @endif
                                @endfor
                            </select>
                            <small class="text-danger" id="msg_hari"></small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div id="namaLaporan" class="col-md-6">
                        <div class="form-group w-100">
                            <label class="form-label" for="laporan">Nama Laporan</label>
                            <select class="form-control select2 w-100" name="laporan" id="laporan">
                                <option value="">---</option>
                                @foreach ($laporan as $lap)
                                    <option value="{{ $lap->file }}">
                                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}.
                                        {{ $lap->nama_laporan }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_laporan"></small>
                        </div>
                    </div>
                    <div class="col-md-6" id="subLaporan">
                        <div class="form-group  w-100">
                            <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
                            <select class="form-control select2 w-100" name="sub_laporan" id="sub_laporan">
                                <option value="">---</option>
                            </select>
                            <small class="text-danger" id="msg_sub_laporan"></small>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="type" id="type" value="pdf">
            </form>

            <div class="d-flex justify-content-end">
                @if (in_array('laporan.simpan_saldo', Session::get('tombol')))
                    <button type="button" id="SimpanSaldo" class="btn btn-sm btn-danger mr-1">Simpan Saldo</button>
                @endif
                @if (in_array('laporan.download_excel', Session::get('tombol')))
                    <button type="button" id="Excel" class="btn btn-sm btn-success mr-1">Excel</button>
                @endif
                @if (in_array('laporan.preview_pdf', Session::get('tombol')))
                    <button type="button" id="Preview" class="btn btn-sm btn-info">Preview</button>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-2" id="LayoutPreview">
            <div class="p-5"></div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('change', '#tahun, #bulan', function(e) {
            e.preventDefault()

            var file = $('select#laporan').val()
            subLaporan(file)
        })

        $(document).on('change', '#laporan', function(e) {
            e.preventDefault()

            var file = $(this).val()
            subLaporan(file)
        })

        function subLaporan(file) {
            var tahun = $('select#tahun').val()
            var bulan = $('select#bulan').val()

            if (file == 'calk') {
                $('#namaLaporan').removeClass('col-md-6')
                $('#namaLaporan').addClass('col-md-12')
                $('#subLaporan').removeClass('col-md-6')
                $('#subLaporan').addClass('col-md-12')
            } else {
                $('#namaLaporan').removeClass('col-md-12')
                $('#namaLaporan').addClass('col-md-6')
                $('#subLaporan').removeClass('col-md-12')
                $('#subLaporan').addClass('col-md-6')
            }

            $('#subLaporan').html('')
            $.get('/pelaporan/sub_laporan/' + file + '?tahun=' + tahun + '&bulan=' + bulan, function(result) {
                $('#subLaporan').html(result)
            })
        }

        $(document).on('click', '#Preview', async function(e) {
            e.preventDefault()

            $(this).parent('div').parent('div').find('form').find('#type').val('pdf')
            var file = $('select#laporan').val()
            if (file == 'calk') {
                await $('textarea#sub_laporan').val(quill.container.firstChild.innerHTML)
            }

            var form = $('#FormPelaporan')
            if (file != '') {
                form.submit()
            }
        })

        $(document).on('click', '#Excel', async function(e) {
            e.preventDefault()

            $(this).parent('div').parent('div').find('form').find('#type').val('excel')
            var file = $('select#laporan').val()
            if (file == 'calk') {
                await $('textarea#sub_laporan').val(quill.container.firstChild.innerHTML)
            }

            var form = $('#FormPelaporan')
            console.log(form.serialize())
            if (file != '') {
                form.submit()
            }
        })

        let childWindow, loading;
        $(document).on('click', '#SimpanSaldo', function(e) {
            e.preventDefault()

            var tahun = $('select#tahun').val()
            loading = Swal.fire({
                title: "Mohon Menunggu..",
                html: "Menyimpan Saldo Januari sampai Desember Th. " + tahun,
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })

            childWindow = window.open('/simpan_saldo?bulan=00&tahun=' + tahun, '_blank');
        })

        window.addEventListener('message', function(event) {
            if (event.data === 'closed') {
                loading.close()
                window.location.reload()
            }
        })
    </script>
@endsection
