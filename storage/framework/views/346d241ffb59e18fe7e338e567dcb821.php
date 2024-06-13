

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div id="akun">
                <ul>
                    <?php $__currentLoopData = $akun1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($lev1->kode_akun); ?>. <?php echo e($lev1->nama_akun); ?>

                            <ul>
                                <?php $__currentLoopData = $lev1->akun2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($lev2->kode_akun); ?>. <?php echo e($lev2->nama_akun); ?>

                                        <ul>
                                            <?php $__currentLoopData = $lev2->akun3; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($lev3->kode_akun); ?>. <?php echo e($lev3->nama_akun); ?>

                                                    <ul>
                                                        <?php $__currentLoopData = $lev3->rek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <li><?php echo e($rek->kode_akun); ?>. <?php echo e($rek->nama_akun); ?></li>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <script>
        $('#akun').jstree();
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\demo\resources\views/sop/coa.blade.php ENDPATH**/ ?>