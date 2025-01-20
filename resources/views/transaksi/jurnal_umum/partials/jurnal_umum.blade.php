<div class="card-body">
    <form action="/transaksi" method="post" id="FormTransaksi">
        @csrf

        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="tgl_transaksi">Tgl Transaksi</label>
                    <input autocomplete="off" type="text" name="tgl_transaksi" id="tgl_transaksi"
                        class="form-control form-control-sm date" value="{{ date('d/m/Y') }}">
                    <small class="text-danger" id="msg_tgl_transaksi"></small>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" for="jenis_transaksi">Jenis Transaksi</label>
                    <select class="form-control select2" name="jenis_transaksi" id="jenis_transaksi">
                        <option value="">-- Pilih Jenis Transaksi --</option>
                        @foreach ($jenis_transaksi as $jt)
                            <option value="{{ $jt->id }}">
                                {{ $jt->nama_jt }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-danger" id="msg_jenis_transaksi"></small>
                </div>
            </div>
        </div>
        <div class="row" id="kd_rekening">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" for="sumber_dana">Sumber Dana</label>
                    <select class="form-control select2" name="sumber_dana" id="sumber_dana">
                        <option value="">-- Sumber Dana --</option>
                    </select>
                    <small class="text-danger" id="msg_sumber_dana"></small>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="form-label" for="disimpan_ke">Disimpan Ke</label>
                    <select class="form-control select2" name="disimpan_ke" id="disimpan_ke">
                        <option value="">-- Disimpan Ke --</option>
                    </select>
                    <small class="text-danger" id="msg_disimpan_ke"></small>
                </div>
            </div>
        </div>
        <div class="row" id="form_nominal">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <input autocomplete="off" type="text" name="keterangan" id="keterangan"
                        class="form-control form-control-sm">
                    <small class="text-danger" id="msg_keterangan"></small>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="nominal">Nominal Rp.</label>
                    <input autocomplete="off" type="text" name="nominal" id="nominal"
                        class="form-control form-control-sm">
                    <small class="text-danger" id="msg_nominal"></small>
                </div>
            </div>
        </div>
    </form>

    @if (in_array('jurnal_umum.simpan_transaksi', Session::get('tombol')))
        <div class="d-flex justify-content-end">
            @if (in_array('jurnal_umum.simpan_transaksi', Session::get('tombol')))
                <button type="button" id="SimpanTransaksi" class="btn btn-sm btn-warning">Simpan Transaksi</button>
            @endif
        </div>
    @endif

</div>

<script>
    $(document).ready(function() {
        $(".date").flatpickr({
            dateFormat: "d/m/Y"
        })

        $("#nominal").maskMoney({
            allowNegative: true
        });

        $('.select2').select2({
            theme: 'bootstrap-5'
        })
    })
</script>
