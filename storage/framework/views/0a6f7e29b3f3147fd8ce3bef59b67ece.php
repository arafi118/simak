<?php
    use App\Utils\Keuangan;
    $pembulatan = (string) $kec->pembulatan;

    $sistem = 'auto';
    if (Keuangan::startWith($pembulatan, '+')) {
        $sistem = 'keatas';
        $pembulatan = intval($pembulatan);
    }

    if (Keuangan::startWith($pembulatan, '-')) {
        $sistem = 'kebawah';
        $pembulatan = intval($pembulatan * -1);
    }
?>

<form action="/pengaturan/pinjaman/<?php echo e($kec->id); ?>" method="post" id="FormPinjaman">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="row">
        <div class="col-md-6">
            <div class="input-group input-group-static my-3">
                <label for="default_jasa">Default Jasa (%)</label>
                <input autocomplete="off" type="number" name="default_jasa" id="default_jasa" class="form-control"
                    value="<?php echo e($kec->def_jasa); ?>">
                <small class="text-danger" id="msg_default_jasa"></small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-static my-3">
                <label for="default_jangka">Default Jangka (%)</label>
                <input autocomplete="off" type="number" name="default_jangka" id="default_jangka" class="form-control"
                    value="<?php echo e($kec->def_jangka); ?>">
                <small class="text-danger" id="msg_default_jangka"></small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="my-2">
                <label class="form-label" for="pembulatan">Pembulatan</label>
                <select class="form-control" name="pembulatan" id="pembulatan">
                    <option <?php echo e($pembulatan == '500' ? 'selected' : ''); ?> value="500">500</option>
                    <option <?php echo e($pembulatan == '1000' ? 'selected' : ''); ?> value="1000">1000</option>
                </select>
                <small class="text-danger" id="msg_pembulatan"></small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="my-2">
                <label class="form-label" for="sistem">Sistem Pembulatan</label>
                <select class="form-control" name="sistem" id="sistem">
                    <option <?php echo e($sistem == 'auto' ? 'selected' : ''); ?> value="">Auto</option>
                    <option <?php echo e($sistem == 'keatas' ? 'selected' : ''); ?> value="+">Keatas</option>
                    <option <?php echo e($sistem == 'kebawah' ? 'selected' : ''); ?> value="-">Kebawah</option>
                </select>
                <small class="text-danger" id="msg_sistem"></small>
            </div>
        </div>
    </div>
</form>

<div class="d-flex justify-content-end">
    <button type="button" id="SimpanPinjaman" data-target="#FormPinjaman"
        class="btn btn-sm btn-github mb-0 btn-simpan">
        Simpan Perubahan
    </button>
</div>
<?php /**PATH C:\laragon\www\demo\resources\views/sop/partials/_pinjaman.blade.php ENDPATH**/ ?>