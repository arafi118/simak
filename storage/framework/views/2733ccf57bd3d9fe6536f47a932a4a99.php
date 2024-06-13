<div class="col-sm-6">
    <div class="form-group">
        <label class="form-label" for="sumber_dana">Sumber Dana</label>
        <select class="form-control select2" name="sumber_dana" id="sumber_dana">
            <option value="">-- <?php echo e($label1); ?> --</option>
            <?php $__currentLoopData = $rek1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($r1->kode_akun); ?>">
                    <?php echo e($r1->kode_akun); ?>. <?php echo e($r1->nama_akun); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_sumber_dana"></small>
    </div>
</div>
<div class="col-sm-6">
    <div class="form-group">
        <label class="form-label" for="disimpan_ke"><?php echo e($label2); ?></label>
        <select class="form-control select2" name="disimpan_ke" id="disimpan_ke">
            <option value="">-- <?php echo e($label2); ?> --</option>
            <?php $__currentLoopData = $rek2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($r2->kode_akun); ?>">
                    <?php echo e($r2->kode_akun); ?>. <?php echo e($r2->nama_akun); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_disimpan_ke"></small>
    </div>
</div>


<script>
    $('.select2').select2({
        theme: 'bootstrap-5'
    })
</script>
<?php /**PATH C:\laragon\www\demo\resources\views/transaksi/jurnal_umum/partials/rekening.blade.php ENDPATH**/ ?>