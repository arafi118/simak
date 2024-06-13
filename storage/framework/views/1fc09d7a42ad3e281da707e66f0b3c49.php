<?php if($file == 3): ?>
    <div class="form-group">
        <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
        <select class="form-control select2" name="sub_laporan" id="sub_laporan">
            <?php $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="BB_<?php echo e($rek->kode_akun); ?>"><?php echo e($rek->kode_akun); ?>. <?php echo e($rek->nama_akun); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_sub_laporan"></small>
    </div>
<?php elseif($file == 'calk'): ?>
    <div class="my-3">
        <div id="editor">
            <ol>
                <?php echo $keterangan; ?>

            </ol>
        </div>
    </div>

    <textarea name="sub_laporan" id="sub_laporan" class="d-none"></textarea>

    <script>
        quill = new Quill('#editor', {
            theme: 'snow'
        });
    </script>
<?php elseif($file == 5): ?>
    <div class="form-group">
        <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
        <select class="form-control select2" name="sub_laporan" id="sub_laporan">
            <?php $__currentLoopData = $jenis_laporan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $jl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($jl->file); ?>"><?php echo e(str_pad($loop->iteration, 2, '0', STR_PAD_LEFT)); ?>.
                    <?php echo e($jl->nama_laporan); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_sub_laporan"></small>
    </div>
<?php elseif($file == 14): ?>
    <div class="form-group">
        <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
        <select class="form-control select2" name="sub_laporan" id="sub_laporan">
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="EB_<?php echo e($dt['id']); ?>">
                    <?php echo e($dt['title']); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_sub_laporan"></small>
    </div>
<?php elseif($file == 'tutup_buku'): ?>
    <div class="form-group">
        <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
        <select class="form-control select2" name="sub_laporan" id="sub_laporan">
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($dt['file']); ?>">
                    <?php echo e($dt['title']); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <small class="text-danger" id="msg_sub_laporan"></small>
    </div>
<?php else: ?>
    <div class="form-group">
        <label class="form-label" for="sub_laporan">Nama Sub Laporan</label>
        <select class="form-control select2" name="sub_laporan" id="sub_laporan">
            <option value="">---</option>
        </select>
        <small class="text-danger" id="msg_sub_laporan"></small>
    </div>
<?php endif; ?>


<script>
    $('.select2').select2({
        theme: 'bootstrap-5'
    })
</script>
<?php /**PATH C:\laragon\www\demo\resources\views/pelaporan/partials/sub_laporan.blade.php ENDPATH**/ ?>