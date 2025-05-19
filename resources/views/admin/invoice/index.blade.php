@php
    use App\Utils\Tanggal;
@endphp

@extends('admin.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="/db/invoice" method="post" id="FormInvoice">
                @csrf

                <div class="row">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="tgl_invoice">Tgl Invoice</label>
                            <input autocomplete="off" type="text" name="tgl_invoice" id="tgl_invoice"
                                class="form-control form-control-sm date" value="{{ date('d/m/Y') }}">
                            <small class="text-danger" id="msg_tgl_invoice"></small>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="nomor_invoice">Nomor Invoice</label>
                            <input autocomplete="off" type="text" name="nomor_invoice" id="nomor_invoice"
                                class="form-control form-control-sm" readonly value="{{ $invoice->nomor }}">
                            <small class="text-danger" id="msg_nomor_invoice"></small>
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group">
                            <label class="form-label" for="nama_usaha">Nama Usaha</label>
                            <select class="form-control select2" name="nama_usaha" id="nama_usaha">
                                <option value="">-- Pilih Nama Usaha --</option>
                                @foreach ($usaha as $u)
                                    @php
                                        $value = [
                                            'id' => $u->id,
                                            'tgl_pakai' => Tanggal::tglIndo($u->tgl_pakai),
                                            'biaya' => $u->biaya,
                                            'tagihan_invoice' => $u->tagihan_invoice,
                                        ];
                                    @endphp

                                    <option value="{{ json_encode($value) }}">
                                        {{ $u->nama_usaha }},
                                        {{ $u->d->sebutan_desa->sebutan_desa }} {{ $u->d->nama_desa }},
                                        {{ $u->d->kec->nama_kec }}, {{ $u->d->kec->kabupaten->nama_kab }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_nama_usaha"></small>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label" for="jenis_pembayaran">Jenis Pembayaran</label>
                            <select class="form-control select2" name="jenis_pembayaran" id="jenis_pembayaran">
                                <option value="">-- Pilih Jenis Pembayaran --</option>
                                @foreach ($jenis as $j)
                                    <option value="{{ $j->id }}">
                                        {{ $j->nama_jp }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_jenis_pembayaran"></small>
                        </div>
                    </div>
                    <div class="col-sm-2" id="colPerpanjangan" style="display: none;">
                        <div class="form-group">
                            <label class="form-label" for="perpanjangan">Perpanjangan</label>
                            <select class="form-control select2 w-100" name="perpanjangan" id="perpanjangan">
                                @for ($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">
                                        {{ str_pad($i, 2, '0', STR_PAD_LEFT) }} Bulan
                                    </option>
                                @endfor
                            </select>
                            <small class="text-danger" id="msg_perpanjangan"></small>
                        </div>
                    </div>
                    <div class="col-sm-3" id="colTglPakai">
                        <div class="form-group">
                            <label for="tgl_pakai">Tgl Pakai</label>
                            <input autocomplete="off" type="text" name="tgl_pakai" id="tgl_pakai"
                                class="form-control form-control-sm" readonly value="{{ date('d/m/Y') }}">
                            <small class="text-danger" id="msg_tgl_pakai"></small>
                        </div>
                    </div>
                    <div class="col-sm-3" id="colNominal">
                        <div class="form-group">
                            <label for="nominal">Nominal</label>
                            <input autocomplete="off" type="text" name="nominal" id="nominal"
                                class="form-control form-control-sm">
                            <small class="text-danger" id="msg_nominal"></small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" id="SimpanInvoice" class="btn btn-sm btn-primary">Buat Invoice</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var formatter = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })

        var biaya = {
            1: '{{ $app->harga }}',
            2: '{{ $app->biaya_maintenance }}',
            3: '0',
            4: '{{ $app->biaya_migrasi }}'
        }

        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        $("#nominal").maskMoney({
            allowNegative: true
        });

        $('.select2').select2({
            theme: 'bootstrap-5'
        })

        $(document).on('change', '#nama_usaha, #jenis_pembayaran', function() {
            var nama_usaha = $('#nama_usaha').val();
            var perpanjangan = $('#perpanjangan').val();
            var jenis_pembayaran = $('#jenis_pembayaran').val();

            if (nama_usaha != '') {
                if (jenis_pembayaran == '') {
                    jenis_pembayaran = 3
                }

                nama_usaha = JSON.parse(nama_usaha)
                biaya[2] = nama_usaha.biaya * perpanjangan

                $('#tgl_pakai').val(nama_usaha.tgl_pakai)
                $('#nominal').val(formatter.format(biaya[jenis_pembayaran]))

                if (jenis_pembayaran == '2') {
                    $('#colTglPakai, #colNominal').attr('class', 'col-sm-2')
                    $('#colPerpanjangan').show()
                } else {
                    $('#colTglPakai, #colNominal').attr('class', 'col-sm-3')
                    $('#colPerpanjangan').hide()
                }

                $('.select2-container').css('width', '100%')
                setNoInvoice()
            }
        })

        $(document).on('change', '#perpanjangan', function() {
            var perpanjangan = $(this).val()
            var nama_usaha = $('#nama_usaha').val();
            var jenis_pembayaran = $('#jenis_pembayaran').val();

            nama_usaha = JSON.parse(nama_usaha)
            biaya[2] = nama_usaha.biaya * perpanjangan
            $('#nominal').val(formatter.format(biaya[jenis_pembayaran]))
        })

        $(document).on('change', '#tgl_invoice', function(e) {
            setNoInvoice()
        })

        $(document).on('click', '#SimpanInvoice', function(e) {
            e.preventDefault()

            $('small.text-danger').html('')
            var form = $('#FormInvoice')
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        var id = result.id
                        Swal.fire('Berhasil', result.msg, 'success').then(() => {
                            Swal.fire({
                                title: 'Tambah Invoice Baru?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Ya',
                                cancelButtonText: 'Tidak'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    window.location.reload()
                                } else {
                                    window.location.href = '/db/invoice/' + result.nomor
                                }
                            })
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

        function setNoInvoice() {
            var tgl = $('#tgl_invoice').val()
            var nama_usaha = $('#nama_usaha').val();

            nama_usaha = JSON.parse(nama_usaha)
            var lokasi = nama_usaha.id

            $.get('/db/nomor_invoice', {
                'tgl_invoice': tgl,
                'lokasi': lokasi
            }, function(result) {
                $('#nomor_invoice').val(result.nomor)
            })
        }
    </script>
@endsection
