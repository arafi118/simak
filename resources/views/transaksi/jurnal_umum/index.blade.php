@extends('layouts.base')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <div class="card" id="formJurnalUmum">
                @include('transaksi.jurnal_umum.partials.jurnal_umum')
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="font-small-3">Saldo:</div>
                        <div class="font-small-3 fw-bold">
                            Rp. <span id="saldo">0.00</span>
                        </div>
                    </div>
                    <hr class="horizontal dark">
                    <div class="font-small-3 fw-bold text-center">Cetak Buku Bantu</div>
                    <hr class="horizontal dark">

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="tahun">Tahunan</label>
                                <select class="form-control select2" name="tahun" id="tahun">
                                    @php
                                        $tgl_pakai = $usaha->tgl_pakai;
                                        $th_pakai = explode('-', $tgl_pakai)[0];
                                    @endphp
                                    @for ($i = $th_pakai; $i <= date('Y'); $i++)
                                        <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                                            {{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="text-danger" id="msg_tahun"></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="bulan">Bulanan</label>
                                <select class="form-control select2" name="bulan" id="bulan">
                                    <option value="">--</option>
                                    <option {{ date('m') == '01' ? 'selected' : '' }} value="01">01. JANUARI</option>
                                    <option {{ date('m') == '02' ? 'selected' : '' }} value="02">02. FEBRUARI</option>
                                    <option {{ date('m') == '03' ? 'selected' : '' }} value="03">03. MARET</option>
                                    <option {{ date('m') == '04' ? 'selected' : '' }} value="04">04. APRIL</option>
                                    <option {{ date('m') == '05' ? 'selected' : '' }} value="05">05. MEI</option>
                                    <option {{ date('m') == '06' ? 'selected' : '' }} value="06">06. JUNI</option>
                                    <option {{ date('m') == '07' ? 'selected' : '' }} value="07">07. JULI</option>
                                    <option {{ date('m') == '08' ? 'selected' : '' }} value="08">08. AGUSTUS</option>
                                    <option {{ date('m') == '09' ? 'selected' : '' }} value="09">09. SEPTEMBER
                                    </option>
                                    <option {{ date('m') == '10' ? 'selected' : '' }} value="10">10. OKTOBER</option>
                                    <option {{ date('m') == '11' ? 'selected' : '' }} value="11">11. NOVEMBER
                                    </option>
                                    <option {{ date('m') == '12' ? 'selected' : '' }} value="12">12. DESEMBER
                                    </option>
                                </select>
                                <small class="text-danger" id="msg_bulan"></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="form-label" for="tanggal">Tanggal</label>
                                <select class="form-control select2" name="tanggal" id="tanggal">
                                    <option value="">--</option>
                                    @for ($j = 1; $j <= 31; $j++)
                                        @php $no=str_pad($j, 2, "0" , STR_PAD_LEFT) @endphp
                                        <option value="{{ $no }}">{{ $no }}</option>
                                    @endfor
                                </select>
                                <small class="text-danger" id="msg_tanggal"></small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" id="BtndetailTransaksi" class="btn btn-sm btn-info">
                            Detail Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="notifikasi"></div>

    <div class="modal fade" id="detailTransaksi" tabindex="-1" aria-labelledby="detailTransaksiLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="detailTransaksiLabel">

                    </h1>
                </div>
                <div class="modal-body">
                    <div id="LayoutdetailTransaksi"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="BtnCetakBuktiTransaksi" class="btn btn-sm btn-info">
                        Cetak Bukti Transaksi
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="CetakBuktiTransaksi" tabindex="-1" aria-labelledby="CetakBuktiTransaksiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="CetakBuktiTransaksiLabel">

                    </h1>
                </div>
                <div class="modal-body">
                    <div id="LayoutCetakBuktiTransaksi"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="BtnCetak" class="btn btn-sm btn-info">
                        Print
                    </button>
                    <button type="button" id="BtnCetakBuktiTransaksi" class="btn btn-danger btn-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <form action="/transaksi/reversal" method="post" id="formReversal">
        @csrf

        <input type="hidden" name="rev_idt" id="rev_idt">
        <input type="hidden" name="rev_idtp" id="rev_idtp">
        <input type="hidden" name="rev_id_pinj" id="rev_id_pinj">
    </form>

    <form action="/transaksi/hapus" method="post" id="formHapus">
        @csrf

        <input type="hidden" name="del_idt" id="del_idt">
        <input type="hidden" name="del_idtp" id="del_idtp">
        <input type="hidden" name="del_id_pinj" id="del_id_pinj">
    </form>

    <input type="hidden" name="saldo_trx" id="saldo_trx">
@endsection

@section('script')
    <script>
        var formatter = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })

        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        $("#nominal").maskMoney({
            allowNegative: true
        });

        $(document).on('change', '#jenis_transaksi', function(e) {
            e.preventDefault()

            if ($(this).val().length > 0) {
                $.get('/transaksi/ambil_rekening/' + $(this).val(), function(result) {
                    $('#kd_rekening').html(result)
                })
            }
        })

        $(document).on('change', '#tgl_transaksi', function(e) {
            e.preventDefault()

            var tgl_transaksi = $(this).val().split('/')
            var tahun = tgl_transaksi[2];
            var bulan = tgl_transaksi[1];
            var hari = tgl_transaksi[0];

            $('select#tahun').val(tahun).change()
            $('select#bulan').val(bulan).change()

            if ($('#sumber_dana').val() != '') {
                var sumber_dana = $('#sumber_dana').val();
                var tgl_transaksi = $(this).val().split('/')

                setSaldo(sumber_dana, tgl_transaksi)
            }
        })

        $(document).on('change', '#sumber_dana', function(e) {
            e.preventDefault()
            var sumber_dana = $(this).val()

            if (sumber_dana == '1.2.02.01') {
                $('select#disimpan_ke').val('5.1.07.08').change()
            }

            if (sumber_dana == '1.2.02.02') {
                $('select#disimpan_ke').val('5.1.07.09').change()
            }

            if (sumber_dana == '1.2.02.03') {
                $('select#disimpan_ke').val('5.1.07.10').change()
            }

            var tgl_transaksi = $('#tgl_transaksi').val().split('/')
            setSaldo(sumber_dana, tgl_transaksi)
        })

        $(document).on('change', '#sumber_dana,#disimpan_ke', function(e) {
            e.preventDefault()

            var tgl_transaksi = $('#tgl_transaksi').val()
            var jenis_transaksi = $('#jenis_transaksi').val()
            var sumber_dana = $('#sumber_dana').val()
            var disimpan_ke = $('#disimpan_ke').val()

            $.get('/transaksi/form_nominal/', {
                jenis_transaksi,
                sumber_dana,
                disimpan_ke,
                tgl_transaksi
            }, function(result) {
                $('#form_nominal').html(result)
            })
        })

        $(document).on('change', '#harga_satuan,#jumlah', function(e) {
            var harga = ($('#harga_satuan').val()) ? $('#harga_satuan').val() : 0
            var jumlah = ($('#jumlah').val()) ? $('#jumlah').val() : 0

            harga = parseInt(harga.split(',').join('').split('.00').join(''))

            var harga_perolehan = harga * jumlah
            $('#harga_perolehan').val(formatter.format(harga_perolehan))
        })

        $(document).on('click', '#SimpanTransaksi', function(e) {
            e.preventDefault()
            $('small').html('')
            $('#notifikasi').html('')

            var nominal = $('#nominal').val()
            if (!nominal) {
                nominal = $('#harga_satuan').val()
            }

            var nominal = parseFloat(nominal.split(',').join(''))
            var saldo_rek = parseFloat($('#saldo_trx').val())

            var sumber_dana = $('#sumber_dana').val()
            if (sumber_dana == '1.2.02.01') {
                saldo_rek *= -1;
            }

            if (sumber_dana == '1.2.02.02') {
                saldo_rek *= -1;
            }

            if (sumber_dana == '1.2.02.03') {
                saldo_rek *= -1;
            }

            if (sumber_dana == '1.1.04.01') {
                saldo_rek *= -1;
            }

            if (sumber_dana == '1.1.04.02') {
                saldo_rek *= -1;
            }

            if (sumber_dana == '1.1.04.03') {
                saldo_rek *= -1;
            }

            saldo_rek = 1
            if (saldo_rek >= nominal) {
                var form = $('#FormTransaksi')
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(result) {
                        if (result.success) {
                            Swal.fire('Berhasil', result.msg, 'success').then(() => {

                                $('#notifikasi').html(result.view)
                                var sumber_dana = $('#sumber_dana').val()
                                var tgl_transaksi = $('#tgl_transaksi').val().split('/')
                                setSaldo(sumber_dana, tgl_transaksi)

                                $('#nominal').val('0')
                            })
                        } else {
                            Swal.fire('Error', result.msg, 'error')
                        }
                    },
                    error: function(result) {
                        const respons = result.responseJSON;

                        Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error')
                        $.map(respons, function(res, key) {
                            $('#' + key).parent('.form-group.form-group-static').addClass(
                                'is-invalid')
                            $('#msg_' + key).html(res)
                        })
                    }
                })
            } else {
                Swal.fire('Error', 'Nominal transaksi melebihi saldo', 'error')
            }

        })

        $(document).on('click', '#SimpanEditTransaksi', function(e) {
            e.preventDefault()
            $('small').html('')
            $('#notifikasi').html('')

            var form = $('#FormEditTransaksi')
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire('Berhasil', result.msg, 'success').then(() => {
                            childWindow = window.open(
                                '/simpan_saldo?bulan=' + result
                                .bulan + '&tahun=' +
                                result.tahun + '&kode_akun=' +
                                result.kode_akun, '_blank');
                        })

                        $('#notifikasi').html(result.notif)
                        $('#saldo').html(formatter.format(0))
                        $('#formJurnalUmum').html(result.view)
                    } else {
                        Swal.fire('Error', result.msg, 'error')
                    }
                },
                error: function(result) {
                    const respons = result.responseJSON;

                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error')
                    $.map(respons, function(res, key) {
                        $('#' + key).parent('.form-group.form-group-static').addClass(
                            'is-invalid')
                        $('#msg_' + key).html(res)
                    })
                }
            })
        })

        $(document).on('change', '#nama_barang', function(e) {
            var value = $(this).val().split('#')

            var id = value[0]
            var unit = parseInt(value[1])
            var nilai_buku = parseInt(value[2])

            var harga = nilai_buku / unit

            $('#unit').attr('max', unit)

            $('#unit').val(unit)
            $('#harsat').val(harga)
            $('#_nilai_buku').val(nilai_buku)
            $('#nilai_buku').val(formatter.format(nilai_buku))
            $('#harga_jual').val(formatter.format(nilai_buku))
        })

        $(document).on('change', '#unit', function() {
            var max = parseInt($(this).attr('max'))
            var unit = parseInt($(this).val())

            if (unit > max) {
                $(this).val(max)
                unit = max
            }

            if (unit < 1) {
                $(this).val(max)
                unit = max
            }

            var harga = parseInt($('#harsat').val())
            var nilai_buku = unit * harga

            $('#_nilai_buku').val(nilai_buku)
            $('#nilai_buku').val(formatter.format(nilai_buku))
            $('#harga_jual').val(formatter.format(nilai_buku))
        })

        $(document).on('change', '#alasan', function() {
            var status = $(this).val()

            var col_harga_jual = false
            if (status == "dijual") {
                var col_harga_jual = true

                $('#col_harga_jual').find('label[for="harga_jual"]').text('Harga Jual')
            }

            if (status == "revaluasi") {
                var col_harga_jual = true

                $('#col_harga_jual').find('label[for="harga_jual"]').text('Harga Revaluasi')
            }

            if (col_harga_jual) {
                $('#col_nilai_buku,#col_unit').attr('class', 'col-sm-4')
                $('#col_harga_jual').show()
                $("#col_harga_jual").focus()
            } else {
                $('#col_nilai_buku,#col_unit').attr('class', 'col-sm-6')
                $('#col_harga_jual').hide()
            }
        })

        $(document).on('click', '#BtndetailTransaksi', function(e) {
            var tahun = $('select#tahun').val()
            var bulan = $('select#bulan').val()
            var hari = $('select#tanggal').val()
            var kode_akun = $('#sumber_dana').val()

            if (kode_akun != '') {
                $.ajax({
                    url: '/transaksi/detail_transaksi',
                    type: 'get',
                    data: {
                        tahun,
                        bulan,
                        hari,
                        kode_akun
                    },
                    success: function(result) {
                        $('#detailTransaksi').modal('show')

                        $('#detailTransaksiLabel').html(result.label)
                        $('#LayoutdetailTransaksi').html(result.view)

                        $('#CetakBuktiTransaksiLabel').html(result.label)
                        $('#LayoutCetakBuktiTransaksi').html(result.cetak)
                    }
                })
            }
        })

        $(document).on('click', '#BtnCetakBuktiTransaksi', function(e) {
            e.preventDefault()

            $('#CetakBuktiTransaksi').modal('toggle')
        })

        $(document).on('click', '.btn-struk', function(e) {
            e.preventDefault()

            var idtp = $(this).attr('data-idtp')
            Swal.fire({
                title: "Cetak Kuitansi Angsuran",
                showDenyButton: true,
                confirmButtonText: "Biasa",
                denyButtonText: "Dot Matrix",
                confirmButtonColor: "#3085d6",
                denyButtonColor: "#3085d6",
            }).then((result) => {
                if (result.isConfirmed) {
                    open_window('/transaksi/angsuran/struk/' + idtp)
                } else if (result.isDenied) {
                    open_window('/transaksi/angsuran/struk_matrix/' + idtp)
                }
            });
        })

        $(document).on('click', '.btn-link', function(e) {
            var action = $(this).attr('data-action')

            open_window(action)
        })

        $(document).on('click', '.btn-reversal', function(e) {
            e.preventDefault()

            var idt = $(this).attr('data-idt')
            $.get('/transaksi/data/' + idt, function(result) {

                $('#rev_idt').val(result.idt)
                $('#rev_idtp').val(result.idtp)
                $('#rev_id_pinj').val(result.id_pinj)
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Setelah menekan tombol Reversal dibawah, maka aplikasi akan membuat transaksi minus (-) senilai Rp. -' +
                        result.jumlah,
                    showCancelButton: true,
                    confirmButtonText: 'Reversal',
                    cancelButtonText: 'Batal',
                    icon: 'warning'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('#formReversal')
                        $.ajax({
                            type: form.attr('method'),
                            url: form.attr('action'),
                            data: form.serialize(),
                            success: function(result) {
                                if (result.success) {
                                    Swal.fire('Berhasil!', result.msg, 'success')
                                        .then(() => {
                                            $('#detailTransaksi').modal('hide')
                                        })
                                }
                            }
                        })
                    }
                })
            })
        })

        $(document).on('click', '.btn-edit', function(e) {
            e.preventDefault()

            var idt = $(this).attr('data-idt')
            $.get('/transaksi/jurnal_umum/' + idt, function(result) {
                if (result.success) {
                    $('#formJurnalUmum').html(result.view)
                    $('#detailTransaksi').modal('hide')

                    $('#sumber_dana').trigger('change')
                }
            })
        })

        $(document).on('click', '#batalEdit', function(e) {
            e.preventDefault()

            $.get('/transaksi/jurnal_umum/', function(result) {
                if (result.success) {
                    $('#formJurnalUmum').html(result.view)
                }
            })
        })

        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault()

            var idt = $(this).attr('data-idt')
            $.get('/transaksi/data/' + idt, function(result) {

                $('#del_idt').val(result.idt)
                $('#del_idtp').val(result.idtp)
                $('#del_id_pinj').val(result.id_pinj)
                Swal.fire({
                    title: 'Peringatan',
                    text: 'Setelah menekan tombol Hapus Transaksi dibawah, maka transaksi ini akan dihapus dari aplikasi secara permanen.',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus Transaksi',
                    cancelButtonText: 'Batal',
                    icon: 'warning'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var form = $('#formHapus')
                        $.ajax({
                            type: form.attr('method'),
                            url: form.attr('action'),
                            data: form.serialize(),
                            success: function(result) {
                                if (result.success) {
                                    Swal.fire('Berhasil!', result.msg, 'success')
                                        .then(() => {
                                            childWindow = window.open(
                                                '/simpan_saldo?bulan=' + result
                                                .bulan + '&tahun=' +
                                                result.tahun + '&kode_akun=' +
                                                result.kode_akun, '_blank');
                                            $('#detailTransaksi').modal('hide')
                                        })
                                }
                            }
                        })
                    }
                })
            })
        })

        $(document).on('click', '#BtnCetak', function(e) {
            e.preventDefault()

            $('#FormCetakDokumenTransaksi').submit()
        })

        function initializeBootstrapTooltip() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]')),
                tooltipList = tooltipTriggerList.map(function(e) {
                    return new bootstrap.Tooltip(e)
                });
        }

        function setSaldo(sumber_dana, tgl_transaksi) {
            var tahun = tgl_transaksi[2];
            var bulan = tgl_transaksi[1];
            var hari = tgl_transaksi[0];

            $.get('/trasaksi/saldo/' + sumber_dana + '?tahun=' + tahun + '&bulan=' + bulan + '&hari=' + hari,
                function(result) {
                    $('#saldo').html(formatter.format(result.saldo))
                    $('#saldo_trx').val(result.saldo)
                })
        }
    </script>
@endsection
