<?php
    use App\Utils\Tanggal;
?>

<?php if($kertas == '80'): ?>
    <style type="text/css">
        @media print {
            @page {
                size: 80mm 90mm;
            }

            body {
                padding: 4px;
            }
        }

        .style1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
        }

        .style2 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6px;
        }
    </style>
<?php else: ?>
    <style type="text/css">
        @media print {
            @page {
                size: 58mm 68mm;
            }

            body {
                padding: 4px;
            }
        }

        .style1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 6px;
        }

        .style2 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 4px;
        }
    </style>
<?php endif; ?>

<style type="text/css">
    .top {
        border-top: thin ridge #000000;
    }

    .bottom {
        border-bottom: thin ridge #000000;
    }

    .left {
        border-left: thin ridge #000000;
    }

    .right {
        border-right: thin ridge #000000;
    }

    .allborder {
        border: thin ridge #000000;
    }

    .center {
        text-align: center;
    }
</style>

<body onload="window.print()">
    <table width="100%" action="" border="0" align="center" cellpadding="1" cellspacing="0" class="style1">

        <tr>
            <td colspan="5" class="bottom" align="center">
                <b><?php echo e(strtoupper($kec->nama_lembaga_sort . ' ' . $kec->nama_kec)); ?></b>
                <br>
                <b><?php echo e($kec->alamat_kec); ?></b>
                <br>
                <?php echo e($kec->nomor_bh); ?>

            </td>
        </tr>

        <tr>
            <td colspan="5" class="bottom" align="center">
                <b>K U I T A N S I</b>
            </td>
        </tr>

        <tr>
            <td width="24%">No</td>
            <td width="2%" align="center">:</td>
            <td width="24%"><?php echo e($trx->idt . '/' . $jenis); ?></td>
            <td colspan="2" width="50%">&nbsp;</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td align="center">:</td>
            <td><?php echo e(Tanggal::tglLatin($trx->tgl_transaksi)); ?></td>
            <td colspan="2" width="50%">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>

        <tr>
            <td>Telah Terima Dari</td>
            <td align="center">:</td>
            <td colspan="3"><?php echo e($dari); ?></td>
        </tr>

        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>

        <tr>
            <td>Uang Sejumlah</td>
            <td align="center">:</td>
            <td colspan="3">Rp. <?php echo e(number_format($trx->jumlah, 2)); ?></td>
        </tr>
        <tr>
            <td>Keperluan</td>
            <td align="center">:</td>
            <td colspan="3"><?php echo e(ucwords($trx->keterangan_transaksi)); ?></td>
        </tr>
        <tr>
            <td colspan="5">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="5">Terbilang : </td>
        </tr>
        <tr>
            <th colspan="5" class="style2">
                <?php echo e(strtoupper($keuangan->terbilang($trx->jumlah))); ?> RUPIAH
            </th>
        </tr>

        <tr>
            <td colspan="5" height="10">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="3" align="center">
                Dibayar Oleh
            </td>
            <td colspan="2" align="center">
                Diterima Oleh
            </td>
        </tr>

        <tr>
            <td colspan="5" height="30">&nbsp;</td>
        </tr>

        <tr>
            <td colspan="3" align="center">
                <b><?php echo e($dibayar); ?></b>
            </td>
            <td colspan="2" align="center">
                <b><?php echo e($oleh); ?></b>
            </td>
        </tr>
    </table>

    <title>K U I T A N S I</title>
</body>
<?php /**PATH C:\laragon\www\demo\resources\views/transaksi/dokumen/kuitansi_thermal.blade.php ENDPATH**/ ?>