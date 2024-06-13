@if ($relasi)
    <div class="col-sm-6">
        <div class="form-group">
            <label for="relasi">Relasi</label>
            <input autocomplete="off" type="text" name="relasi" id="relasi" class="form-control form-control-sm">
            <small class="text-danger" id="msg_relasi"></small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <input autocomplete="off" type="text" name="keterangan" id="keterangan"
                class="form-control form-control-sm" value="{{ $keterangan_transaksi }}">
            <small class="text-danger" id="msg_keterangan"></small>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label for="nominal">Nominal Rp.</label>
            <input autocomplete="off" type="text" name="nominal" id="nominal" class="form-control form-control-sm">
            <small class="text-danger" id="msg_nominal"></small>
        </div>
    </div>
@else
    <input type="hidden" name="relasi" id="relasi" value="">
    <div class="col-sm-12">
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <input autocomplete="off" type="text" name="keterangan" id="keterangan"
                class="form-control form-control-sm" value="{{ $keterangan_transaksi }}">
            <small class="text-danger" id="msg_keterangan"></small>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label for="nominal">Nominal Rp.</label>
            <input autocomplete="off" type="text" name="nominal" id="nominal" class="form-control form-control-sm"
                value="{{ number_format($susut, 2) }}">
            <small class="text-danger" id="msg_nominal"></small>
        </div>
    </div>
@endif

<script>
    $("#nominal").maskMoney({
        allowNegative: true
    });
</script>
