@php
    use App\Utils\Tanggal;
@endphp

<div class="card-body">
    <form action="/transaksi/{{ $trx->idt }}" method="post" id="FormEditTransaksi">
        @method('PUT')
        @csrf

        <input type="hidden" name="jenis_transaksi" id="jenis_transaksi" value="3">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="tgl_transaksi">Tgl Transaksi</label>
                    <input autocomplete="off" type="text" name="tgl_transaksi" id="tgl_transaksi"
                        class="form-control form-control-sm date" value="{{ Tanggal::tglIndo($trx->tgl_transaksi) }}">
                    <small class="text-danger" id="msg_tgl_transaksi"></small>
                </div>
            </div>
        </div>
        <div class="row" id="kd_rekening">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" for="sumber_dana">Sumber Dana</label>
                    <select class="form-control select2" name="sumber_dana" id="sumber_dana">
                        <option value="">-- Sumber Dana --</option>
                        @foreach ($rekening as $r1)
                            <option value="{{ $r1->kode_akun }}"
                                {{ $r1->kode_akun == $trx->rekening_kredit ? 'selected' : '' }}>
                                {{ $r1->kode_akun }}. {{ $r1->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="msg_sumber_dana"></small>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" for="disimpan_ke">Disimpan Ke</label>
                    <select class="form-control select2" name="disimpan_ke" id="disimpan_ke">
                        <option value="">-- Disimpan Ke --</option>
                        @foreach ($rekening as $r2)
                            <option value="{{ $r2->kode_akun }}"
                                {{ $r2->kode_akun == $trx->rekening_debit ? 'selected' : '' }}>
                                {{ $r2->kode_akun }}. {{ $r2->nama_akun }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="msg_disimpan_ke"></small>
                </div>
            </div>
        </div>
        <div class="row" id="form_nominal">
            @if ($relasi)
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="relasi">Relasi</label>
                        <input autocomplete="off" type="text" name="relasi" id="relasi"
                            class="form-control form-control-sm" value="{{ $trx->relasi }}">
                        <small class="text-danger" id="msg_relasi"></small>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input autocomplete="off" type="text" name="keterangan" id="keterangan"
                            class="form-control form-control-sm" value="{{ $trx->keterangan_transaksi }}">
                        <small class="text-danger" id="msg_keterangan"></small>
                    </div>
                </div>
            @else
                <input type="hidden" name="relasi" id="relasi" value="">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label for="keterangan">Keterangan</label>
                        <input autocomplete="off" type="text" name="keterangan" id="keterangan"
                            class="form-control form-control-sm" value="{{ $trx->keterangan_transaksi }}">
                        <small class="text-danger" id="msg_keterangan"></small>
                    </div>
                </div>
            @endif
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="nominal">Nominal Rp.</label>
                    <input autocomplete="off" type="text" name="nominal" id="nominal"
                        class="form-control form-control-sm" value="{{ number_format($trx->jumlah, 2) }}">
                    <small class="text-danger" id="msg_nominal"></small>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-end">
        <button type="button" id="SimpanEditTransaksi" class="btn btn-sm btn-warning">Simpan Transaksi</button>
    </div>
</div>

<script>
    $(".date").flatpickr({
        dateFormat: "d/m/Y"
    })

    $("#nominal").maskMoney({
        allowNegative: true
    });

    $('.select2').select2({
        theme: 'bootstrap-5'
    })
</script>
