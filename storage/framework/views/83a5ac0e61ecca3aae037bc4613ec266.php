<?php
    use App\Utils\Inventaris as Inv;
?>

<input type="hidden" name="_nilai_buku" id="_nilai_buku">
<input type="hidden" name="harsat" id="harsat">
<input type="hidden" name="relasi" id="relasi">
<div class="col-sm-8">
    <div class="form-group">
        <label class="form-label" for="nama_barang">Nama Barang</label>
        <select class="form-control select2" name="nama_barang" id="nama_barang">
            <option value="">-- Pilih Nama Barang --</option>
            <?php $__currentLoopData = $inventaris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $nilai_buku = Inv::nilaiBuku($tgl_transaksi, $inv);
                ?>
                <option value="<?php echo e($inv->id); ?>#<?php echo e($inv->unit); ?>#<?php echo e($nilai_buku); ?>">
                    <?php echo e($inv->nama_barang); ?> (<?php echo e($inv->unit); ?> unit x
                    <?php echo e(number_format($inv->harsat)); ?>) | NB. <?php echo e(number_format($nilai_buku, 2)); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_nama_barang"></small>
    </div>
</div>
<div class="col-sm-4">
    <div class="form-group">
        <label class="form-label" for="alasan">Alasan</label>
        <select class="form-control select2" name="alasan" id="alasan">
            <option value="">-- Alasan Penghapusan --</option>
            <option value="hapus">Hapus</option>
            <option value="hilang">Hilang</option>
            <option value="rusak">Rusak</option>
            <option value="dijual">Dijual</option>
            <option value="revaluasi">Revaluasi</option>
        </select>
        <small class="text-danger" id="msg_alasan"></small>
    </div>
</div>
<div id="col_unit" class="col-sm-6">
    <div class="form-group">
        <label for="unit">Jumlah (unit)</label>
        <input autocomplete="off" type="number" name="unit" id="unit" class="form-control form-control-sm">
        <small class="text-danger" id="msg_unit"></small>
    </div>
</div>
<div id="col_nilai_buku" class="col-sm-6">
    <div class="form-group">
        <label for="nilai_buku">Nilai Buku</label>
        <input autocomplete="off" readonly disabled type="text" name="nilai_buku" id="nilai_buku"
            class="form-control form-control-sm">
        <small class="text-danger" id="msg_nilai_buku"></small>
    </div>
</div>
<div id="col_harga_jual" class="col-sm-4" style="display: none">
    <div class="form-group">
        <label for="harga_jual">Harga Jual</label>
        <input autocomplete="off" type="text" name="harga_jual" id="harga_jual" class="form-control form-control-sm">
        <small class="text-danger" id="msg_harga_jual"></small>
    </div>
</div>

<script>
    $("#nilai_buku").maskMoney({
        allowNegative: true
    });

    $("#harga_jual").maskMoney({
        allowNegative: true
    });

    $('.select2').select2({
        theme: 'bootstrap-5'
    })
</script>
<?php /**PATH C:\laragon\www\demo\resources\views/transaksi/jurnal_umum/partials/form_hapus_inventaris.blade.php ENDPATH**/ ?>