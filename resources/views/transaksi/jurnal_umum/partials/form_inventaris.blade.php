@php
    $unit = 0;
    $nominal = 0;
    $umur_eko = 0;
    $nama_barang = '';
    $harga_satuan = 0;
    $value_relasi = '';
    $harga_perolehan = 0;
    if (Session::get('edit') == 'true') {
        $unit = Session::get('inv')->unit;
        $nominal = Session::get('transaksi')->jumlah;
        $umur_eko = Session::get('inv')->umur_ekonomis;
        $nama_barang = Session::get('inv')->nama_barang;
        $harga_satuan = Session::get('inv')->harsat;
        $value_relasi = Session::get('transaksi')->relasi;
        $harga_perolehan = $harga_satuan * $unit;
    }

@endphp

@if ($relasi)
    <div class="col-sm-4">
        <div class="form-group">
            <label for="relasi">Relasi</label>
            <input autocomplete="off" type="text" name="relasi" id="relasi" value="{{ $value_relasi }}"
                class="form-control form-control-sm">
            <small class="text-danger" id="msg_relasi"></small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input autocomplete="off" type="text" name="nama_barang" value="{{ $nama_barang }}" id="nama_barang"
                class="form-control form-control-sm">
            <small class="text-danger" id="msg_nama_barang"></small>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="form-group">
            <label for="jumlah">Jml. Unit</label>
            <input autocomplete="off" type="number" name="jumlah" id="jumlah" value="{{ $unit }}"
                class="form-control form-control-sm">
            <small class="text-danger" id="msg_jumlah"></small>
        </div>
    </div>
@else
    <input type="hidden" name="relasi" id="relasi" value="{{ $value_relasi }}">
    <div class="col-sm-6">
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input autocomplete="off" type="text" name="nama_barang" id="nama_barang" value="{{ $nama_barang }}"
                class="form-control form-control-sm">
            <small class="text-danger" id="msg_nama_barang"></small>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            <label for="jumlah">Jml. Unit</label>
            <input autocomplete="off" type="number" name="jumlah" id="jumlah" value="{{ $unit }}"
                class="form-control form-control-sm">
            <small class="text-danger" id="msg_jumlah"></small>
        </div>
    </div>
@endif
<div class="col-sm-4">
    <div class="form-group">
        <label for="harga_satuan">Harga Satuan</label>
        <input autocomplete="off" type="text" name="harga_satuan" id="harga_satuan"
            value="{{ number_format($harga_satuan, 2) }}" class="form-control form-control-sm">
        <small class="text-danger" id="msg_harga_satuan"></small>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        <label for="umur_ekonomis">Umur Eko. (bulan)</label>
        <input autocomplete="off" type="number" name="umur_ekonomis" id="umur_ekonomis" value="{{ $umur_eko }}"
            class="form-control form-control-sm">
        <small class="text-danger" id="msg_umur_ekonomis"></small>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        <label for="harga_perolehan">Harga Perolehan</label>
        <input autocomplete="off" type="text" readonly disabled name="harga_perolehan" id="harga_perolehan"
            value="{{ number_format($harga_perolehan, 2) }}" class="form-control form-control-sm">
        <small class="text-danger" id="msg_harga_perolehan"></small>
    </div>
</div>

<script>
    $("#harga_satuan").maskMoney({
        allowNegative: true
    });
</script>
