<?php
    use App\Utils\Tanggal;
    $total_saldo = 0;

    if ($rek->jenis_mutasi == 'debet') {
        $saldo_awal_tahun = $saldo['debit'] - $saldo['kredit'];
        $saldo_awal_bulan = $d_bulan_lalu - $k_bulan_lalu;
        $total_saldo = $saldo_awal_tahun + $saldo_awal_bulan;
    } else {
        $saldo_awal_tahun = $saldo['kredit'] - $saldo['debit'];
        $saldo_awal_bulan = $k_bulan_lalu - $d_bulan_lalu;
        $total_saldo = $saldo_awal_tahun + $saldo_awal_bulan;
    }

    $total_debit = 0;
    $total_kredit = 0;
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0" class="table table-striped midle">
    <thead class="bg-dark text-white">
        <tr>
            <td height="40" align="center" width="40">No</td>
            <td align="center" width="100">Tanggal</td>
            <td align="center" width="100">Kode Akun</td>
            <td align="center">Keterangan</td>
            <td align="center" width="70">Kode Trx.</td>
            <td align="center" width="140">Debit</td>
            <td align="center" width="140">Kredit</td>
            <td align="center" width="150">Saldo</td>
            <td align="center" width="40">Ins</td>
            <td align="center" width="170">&nbsp;</td>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td align="center"></td>
            <td align="center"><?php echo e(Tanggal::tglIndo($tahun . '-01-01')); ?></td>
            <td align="center"></td>
            <td>Komulatif Transaksi Awal Tahun <?php echo e($tahun); ?></td>
            <td>&nbsp;</td>
            <td align="right"><?php echo e(number_format($saldo['debit'], 2)); ?></td>
            <td align="right"><?php echo e(number_format($saldo['kredit'], 2)); ?></td>
            <td align="right"><?php echo e(number_format($saldo_awal_tahun, 2)); ?></td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center"><?php echo e(Tanggal::tglIndo($tahun . '-' . $bulan . '-01')); ?></td>
            <td align="center"></td>
            <td>Komulatif Transaksi s/d Bulan Lalu</td>
            <td>&nbsp;</td>
            <td align="right"><?php echo e(number_format($d_bulan_lalu, 2)); ?></td>
            <td align="right"><?php echo e(number_format($k_bulan_lalu, 2)); ?></td>
            <td align="right"><?php echo e(number_format($total_saldo, 2)); ?></td>
            <td align="center"></td>
            <td align="center"></td>
        </tr>

        <?php $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                if ($trx->rekening_debit == $rek->kode_akun) {
                    $ref = $trx->rekening_kredit;
                    $debit = $trx->jumlah;
                    $kredit = 0;
                } else {
                    $ref = $trx->rekening_debit;
                    $debit = 0;
                    $kredit = $trx->jumlah;
                }

                if ($rek->jenis_mutasi == 'debet') {
                    $_saldo = $debit - $kredit;
                } else {
                    $_saldo = $kredit - $debit;
                }

                $total_saldo += $_saldo;
                $total_debit += $debit;
                $total_kredit += $kredit;

                $kuitansi = false;
                $files = 'bm';
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    !$keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bkm';
                    $kuitansi = true;
                }
                if (
                    !$keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bkk';
                    $kuitansi = true;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    !(
                        $keuangan->startWith($trx->rekening_kredit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                    )
                ) {
                    $files = 'bkm';
                    $kuitansi = true;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.02') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.01')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '1.1.01') &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    $keuangan->startWith($trx->rekening_debit, '5.') &&
                    !(
                        $keuangan->startWith($trx->rekening_kredit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                    )
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    !(
                        $keuangan->startWith($trx->rekening_debit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_debit, '1.1.02')
                    ) &&
                    $keuangan->startWith($trx->rekening_kredit, '1.1.02')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }
                if (
                    !(
                        $keuangan->startWith($trx->rekening_debit, '1.1.01') ||
                        $keuangan->startWith($trx->rekening_debit, '1.1.02')
                    ) &&
                    $keuangan->startWith($trx->rekening_kredit, '4.')
                ) {
                    $files = 'bm';
                    $kuitansi = false;
                }

                $ins = '';
                if (isset($trx->user->ins)) {
                    $ins = $trx->user->ins;
                }
            ?>


            <tr>
                <td align="center"><?php echo e($loop->iteration); ?>.</td>
                <td align="center"><?php echo e(Tanggal::tglIndo($trx->tgl_transaksi)); ?></td>
                <td align="center"><?php echo e($ref); ?></td>
                <td><?php echo e($trx->keterangan_transaksi); ?></td>
                <td align="center"><?php echo e($trx->idt); ?></td>
                <td align="right"><?php echo e(number_format($debit, 2)); ?></td>
                <td align="right"><?php echo e(number_format($kredit, 2)); ?></td>
                <td align="right"><?php echo e(number_format($total_saldo, 2)); ?></td>
                <td align="center"><?php echo e($ins); ?></td>
                <td align="right">
                    <div class="btn-group">
                        <?php if($kuitansi): ?>
                            <?php if($trx->idtp > 0 && $trx->id_pinj != 0): ?>
                                <button type="button" data-idtp="<?php echo e($trx->idtp); ?>"
                                    class="btn btn-info btn-sm btn-tooltip" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <span class="btn-inner--icon"><i class="fas fa-file"></i></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item border-radius-md" target="_blank"
                                            href="/transaksi/dokumen/struk/<?php echo e($trx->idtp); ?>">
                                            Kuitansi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item border-radius-md" target="_blank"
                                            href="/transaksi/dokumen/struk_matrix/<?php echo e($trx->idtp); ?>">
                                            Kuitansi Dot Matrix
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item border-radius-md" target="_blank"
                                            href="/transaksi/dokumen/struk_thermal/<?php echo e($trx->idtp); ?>">
                                            Kuitansi Thermal
                                        </a>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <button type="button" class="btn btn-info btn-sm btn-tooltip" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <span class="btn-inner--icon"><i class="fas fa-file"></i></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <a class="dropdown-item border-radius-md" target="_blank"
                                            href="/transaksi/dokumen/kuitansi/<?php echo e($trx->idt); ?>">
                                            Kuitansi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item border-radius-md" target="_blank"
                                            href="/transaksi/dokumen/kuitansi_thermal/<?php echo e($trx->idt); ?>">
                                            Kuitansi Thermal
                                        </a>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if($trx->idtp > 0 && $trx->id_pinj != 0): ?>
                            <button type="button"
                                data-action="/transaksi/dokumen/<?php echo e($files); ?>_angsuran/<?php echo e($trx->idt); ?>"
                                class="btn btn-warning btn-sm btn-tooltip btn-link" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="<?php echo e($files); ?>" data-container="body"
                                data-animation="true">
                                <span class="btn-inner--icon"><i class="fas fa-file-circle-exclamation"></i></span>
                            </button>
                        <?php else: ?>
                            <button type="button"
                                data-action="/transaksi/dokumen/<?php echo e($files); ?>/<?php echo e($trx->idt); ?>"
                                class="btn btn-warning btn-sm btn-tooltip btn-link" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="<?php echo e($files); ?>" data-container="body"
                                data-animation="true">
                                <span class="btn-inner--icon"><i class="fas fa-file-circle-exclamation"></i></span>
                            </button>
                        <?php endif; ?>

                        <?php if($is_dir): ?>
                            <button type="button" data-idt="<?php echo e($trx->idt); ?>"
                                class="btn btn-warning btn-sm btn-tooltip btn-reversal" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Reversal" data-container="body"
                                data-animation="true">
                                <span class="btn-inner--icon"><i class="fas fa-code-pull-request"></i></span>
                            </button>
                            <?php if(!$is_ben): ?>
                                <button type="button" data-idt="<?php echo e($trx->idt); ?>"
                                    class="btn btn-danger btn-sm btn-tooltip btn-delete" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Hapus" data-container="body"
                                    data-animation="true">
                                    <span class="btn-inner--icon"><i class="fas fa-trash-can"></i></span>
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <tr>
            <td colspan="5">
                <b>Total Transaksi <?php echo e(ucwords($sub_judul)); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($total_debit, 2)); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($total_kredit, 2)); ?></b>
            </td>
            <td colspan="3" rowspan="3" align="center" style="vertical-align: middle">
                <b><?php echo e(number_format($total_saldo, 2)); ?></b>
            </td>
        </tr>

        <tr>
            <td colspan="5">
                <b>Total Transaksi sampai dengan <?php echo e(ucwords($sub_judul)); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($d_bulan_lalu + $total_debit, 2)); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($k_bulan_lalu + $total_kredit, 2)); ?></b>
            </td>
        </tr>

        <tr>
            <td colspan="5">
                <b>Total Transaksi Komulatif sampai dengan Tahun <?php echo e($tahun); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($saldo['debit'] + $d_bulan_lalu + $total_debit, 2)); ?></b>
            </td>
            <td align="right">
                <b><?php echo e(number_format($saldo['kredit'] + $k_bulan_lalu + $total_kredit, 2)); ?></b>
            </td>
        </tr>
    </tbody>

</table>

<script>
    $(document).ready(function() {
        initializeBootstrapTooltip()
    })
</script>
<?php /**PATH C:\laragon\www\demo\resources\views/transaksi/jurnal_umum/partials/jurnal.blade.php ENDPATH**/ ?>