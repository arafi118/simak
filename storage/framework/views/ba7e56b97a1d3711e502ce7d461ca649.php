<?php
    use App\Utils\Keuangan;

    $bulan = Tanggal::namaBulan(date('Y-m-d'));

    $kenaikan = $saldo_kas - $saldo_kas_lalu;

    $text = 'text-success';
    $icon = '<i class="fa-solid fa-caret-up"></i>';
    if ($kenaikan < 0) {
        $text = 'text-danger';
        $icon = '<i class="fa-solid fa-caret-down"></i>';

        $kenaikan *= -1;
    }
?>



<?php $__env->startSection('content'); ?>
    <form action="" method="post" id="defaultForm">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="tgl" id="tgl" value="<?php echo e(date('d/m/Y')); ?>">
    </form>

    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <div class="card-body p-2 position-relative">
                    <div class="row">
                        <div class="col-7 text-left">
                            <p class="font-small-3 mb-1 text-capitalize font-weight-bold">Saldo Kas</p>
                            <h5 class="font-weight-bolder mb-0">
                                Rp <?php echo e(number_format($saldo_kas, 2)); ?>

                            </h5>
                            <span class="font-small-3 text-right <?php echo e($text); ?> font-weight-bolder mt-auto mb-0">
                                <?php echo $icon; ?> <span class="text-secondary">Rp
                                    <?php echo e(number_format($kenaikan, 2)); ?></span>
                            </span>
                        </div>
                        <div class="col-5">
                            <div class="dropdown text-right">
                                <span class="font-small-3 text-secondary"><?php echo e($bulan); ?> <?php echo e(date('Y')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <h6 class="mb-0">Pendapatan dan Beban</h6>
            </div>
        </div>
        <div class="card-body">
            <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="400"
                    style="display: block; box-sizing: border-box; height: 210px; width: 844.4px;" width="1688"></canvas>
            </div>
        </div>
    </div>

    <form action="/pelaporan/preview" method="post" id="FormLaporanDashboard" target="_blank">
        <?php echo csrf_field(); ?>

        <input type="hidden" name="type" id="type" value="pdf">
        <input type="hidden" name="tahun" id="tahun" value="<?php echo e(date('Y')); ?>">
        <input type="hidden" name="bulan" id="bulan" value="<?php echo e(date('m')); ?>">
        <input type="hidden" name="hari" id="hari" value="<?php echo e(date('d')); ?>">
        <input type="hidden" name="laporan" id="laporan" value="">
        <input type="hidden" name="sub_laporan" id="sub_laporan" value="">
    </form>

    <?php
        $p = $saldo[4];
        $b = $saldo[5];
        $surplus = $saldo['surplus'];
    ?>

    <textarea name="msgInvoice" id="msgInvoice" class="d-none"><?php echo e(Session::get('msg')); ?></textarea>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
    <?php if(Session::get('invoice')): ?>
        <script>
            function msgInvoice(number, msg, repeat = 0) {
                $.ajax({
                    type: 'post',
                    url: '<?php echo e($api); ?>/send-text',
                    timeout: 0,
                    headers: {
                        "Content-Type": "application/json"
                    },
                    xhrFields: {
                        withCredentials: true
                    },
                    data: JSON.stringify({
                        token: "DBM-330819-0001",
                        number: number,
                        text: msg
                    }),
                    success: function(result) {
                        if (!result.status) {
                            setTimeout(function() {
                                msgInvoice(number, msg, repeat + 1)
                            }, 1000)
                        }
                    },
                    error: function(result) {
                        if (repeat < 1) {
                            setTimeout(function() {
                                msgInvoice(number, msg, repeat + 1)
                            }, 1000)
                        }
                    }
                })
            }

            msgInvoice("<?php echo e(Session::get('hp_dir')); ?>", $('#msgInvoice').val())
            setTimeout(() => {
                msgInvoice('0882006644656', $('#msgInvoice').val())
            }, 1000);
        </script>
    <?php endif; ?>

    <script>
        var formatter = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        })

        var ctx1 = document.getElementById("chart-line").getContext("2d");

        // Line chart
        new Chart(ctx1, {
            type: "line",
            data: {
                labels: [
                    "Jan",
                    "Feb",
                    "Mar",
                    "Apr",
                    "Mei",
                    "Jun",
                    "Jul",
                    "Agu",
                    "Sep",
                    "Okt",
                    "Nov",
                    "Des",
                ],
                datasets: [{
                        label: "Pendapatan",
                        tension: 0,
                        pointRadius: 5,
                        pointBackgroundColor: "#4CAF50",
                        pointBorderColor: "transparent",
                        borderColor: "#4CAF50",
                        borderWidth: 2,
                        backgroundColor: "transparent",
                        fill: true,
                        data: [
                            "<?php echo e($p['1']); ?>",
                            "<?php echo e($p['2']); ?>",
                            "<?php echo e($p['3']); ?>",
                            "<?php echo e($p['4']); ?>",
                            "<?php echo e($p['5']); ?>",
                            "<?php echo e($p['6']); ?>",
                            "<?php echo e($p['7']); ?>",
                            "<?php echo e($p['8']); ?>",
                            "<?php echo e($p['9']); ?>",
                            "<?php echo e($p['10']); ?>",
                            "<?php echo e($p['11']); ?>",
                            "<?php echo e($p['12']); ?>"
                        ],
                        maxBarThickness: 6
                    },
                    {
                        label: "Beban",
                        tension: 0,
                        borderWidth: 0,
                        pointRadius: 5,
                        pointBackgroundColor: "#fb8c00",
                        pointBorderColor: "transparent",
                        borderColor: "#fb8c00",
                        borderWidth: 2,
                        backgroundColor: "transparent",
                        fill: true,
                        data: [
                            "<?php echo e($b['1']); ?>",
                            "<?php echo e($b['2']); ?>",
                            "<?php echo e($b['3']); ?>",
                            "<?php echo e($b['4']); ?>",
                            "<?php echo e($b['5']); ?>",
                            "<?php echo e($b['6']); ?>",
                            "<?php echo e($b['7']); ?>",
                            "<?php echo e($b['8']); ?>",
                            "<?php echo e($b['9']); ?>",
                            "<?php echo e($b['10']); ?>",
                            "<?php echo e($b['11']); ?>",
                            "<?php echo e($b['12']); ?>"
                        ],
                        maxBarThickness: 6
                    },
                    {
                        label: "Laba",
                        tension: 0,
                        borderWidth: 0,
                        pointRadius: 5,
                        pointBackgroundColor: "#1A73E8",
                        pointBorderColor: "transparent",
                        borderColor: "#1A73E8",
                        borderWidth: 2,
                        backgroundColor: "transparent",
                        fill: true,
                        data: [
                            "<?php echo e($surplus['1']); ?>",
                            "<?php echo e($surplus['2']); ?>",
                            "<?php echo e($surplus['3']); ?>",
                            "<?php echo e($surplus['4']); ?>",
                            "<?php echo e($surplus['5']); ?>",
                            "<?php echo e($surplus['6']); ?>",
                            "<?php echo e($surplus['7']); ?>",
                            "<?php echo e($surplus['8']); ?>",
                            "<?php echo e($surplus['9']); ?>",
                            "<?php echo e($surplus['10']); ?>",
                            "<?php echo e($surplus['11']); ?>",
                            "<?php echo e($surplus['12']); ?>"
                        ],
                        maxBarThickness: 6
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    y: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: false,
                            borderDash: [5, 5],
                            color: '#c1c4ce5c'
                        },
                        ticks: {
                            display: true,
                            padding: 10,
                            color: '#9ca2b7',
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                    x: {
                        grid: {
                            drawBorder: false,
                            display: true,
                            drawOnChartArea: true,
                            drawTicks: true,
                            borderDash: [5, 5],
                            color: '#c1c4ce5c'
                        },
                        ticks: {
                            display: true,
                            color: '#9ca2b7',
                            padding: 10,
                            font: {
                                size: 14,
                                weight: 300,
                                family: "Roboto",
                                style: 'normal',
                                lineHeight: 2
                            },
                        }
                    },
                },
            },
        });

        let childWindow, loading;
        $(document).on('click', '#simpanSaldo', function(e) {
            var link = $(this).attr('data-href')

            loading = Swal.fire({
                title: "Mohon Menunggu..",
                html: "Menyimpan Saldo Januari sampai Desember Th. <?php echo e(date('Y')); ?>",
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            })

            childWindow = window.open(link, '_blank');
        })

        window.addEventListener('message', function(event) {
            if (event.data === 'closed') {
                loading.close()
                window.location.reload()
            }
        })
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\demo\resources\views/dashboard/index.blade.php ENDPATH**/ ?>