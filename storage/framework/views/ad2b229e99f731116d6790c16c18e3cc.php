<?php
    use App\Utils\Keuangan;
    $keuangan = new Keuangan();
?>



<?php $__env->startSection('content'); ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="3" align="center">
                <div style="font-size: 18px;">
                    <b>NERACA</b>
                </div>
                <div style="font-size: 16px;">
                    <b><?php echo e(strtoupper($sub_judul)); ?></b>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="3"></td>
        </tr>
        <tr style="background: #000; color: #fff;">
            <td width="10%">Kode</td>
            <td width="70%">Nama Akun</td>
            <td align="right" width="20%">Saldo</td>
        </tr>
        <tr>
            <td colspan="3" height="1"></td>
        </tr>

        <?php $__currentLoopData = $akun1; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev1): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $sum_akun1 = 0;
            ?>
            <tr style="background: rgb(74, 74, 74); color: #fff;">
                <td height="20" colspan="3" align="center">
                    <b><?php echo e($lev1->kode_akun); ?>. <?php echo e($lev1->nama_akun); ?></b>
                </td>
            </tr>
            <?php $__currentLoopData = $lev1->akun2; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev2): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr style="background: rgb(167, 167, 167); font-weight: bold;">
                    <td><?php echo e($lev2->kode_akun); ?>.</td>
                    <td colspan="2"><?php echo e($lev2->nama_akun); ?></td>
                </tr>

                <?php $__currentLoopData = $lev2->akun3; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lev3): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $sum_saldo = 0;
                    ?>

                    <?php $__currentLoopData = $lev3->rek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rek): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $saldo = $keuangan->komSaldo($rek);
                            if ($rek->kode_akun == '3.2.02.01') {
                                $saldo = $keuangan->laba_rugi($tgl_kondisi);
                            }

                            $sum_saldo += $saldo;
                        ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $bg = 'rgb(230, 230, 230)';
                        if ($loop->iteration % 2 == 0) {
                            $bg = 'rgba(255, 255, 255)';
                        }

                        if ($lev1->lev1 == '1') {
                            $debit += $sum_saldo;
                        } else {
                            $kredit += $sum_saldo;
                        }

                        $sum_akun1 += $sum_saldo;
                    ?>
                    <tr style="background: <?php echo e($bg); ?>;">
                        <td><?php echo e($lev3->kode_akun); ?>.</td>
                        <td><?php echo e($lev3->nama_akun); ?></td>
                        <?php if($sum_saldo < 0): ?>
                            <td align="right">(<?php echo e(number_format($sum_saldo * -1, 2)); ?>)</td>
                        <?php else: ?>
                            <td align="right"><?php echo e(number_format($sum_saldo, 2)); ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <tr style="background: rgb(167, 167, 167); font-weight: bold;">
                <td height="15" colspan="2" align="left">
                    <b>Jumlah <?php echo e($lev1->nama_akun); ?></b>
                </td>
                <td align="right"><?php echo e(number_format($sum_akun1, 2)); ?></td>
            </tr>
            <tr>
                <td colspan="3" height="1"></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr>
            <td colspan="3" style="padding: 0px !important;">
                <table class="p" border="0" width="100%" cellspacing="0" cellpadding="0"
                    style="font-size: 11px;">
                    <tr style="background: rgb(167, 167, 167); font-weight: bold;">
                        <td height="15" width="80%" align="left">
                            <b>Jumlah Liabilitas + Ekuitas </b>
                        </td>
                        <td align="right" width="20%"><?php echo e(number_format($kredit, 2)); ?></td>
                    </tr>
                </table>

                <div style="margin-top: 16px;"></div>
                <?php echo json_decode(str_replace('{tanggal}', $tanggal_kondisi, $kec->ttd->tanda_tangan_pelaporan), true); ?>

            </td>
        </tr>
    </table>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('pelaporan.layout.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\demo\resources\views/pelaporan/view/neraca.blade.php ENDPATH**/ ?>