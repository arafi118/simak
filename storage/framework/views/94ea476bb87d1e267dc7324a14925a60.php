<?php
    function active($path, ...$paths)
    {
        $page = explode('/', Request::path());
        $page = '/' . end($page);
        if ($page == $path) {
            return 'active';
        }

        if (in_array($page, $paths)) {
            return 'active';
        }
    }

?>

<div class="main-menu menu-fixed menu-dark menu-bg-default rounded menu-accordion menu-shadow">
    <div class="main-menu-content">
        <a class="navigation-brand d-none d-md-flex d-lg-flex d-xl-flex justify-content-center" href="/dashboard">
            <div class="avatar avatar-online avatar-md">
                <img class="brand-logo" alt="CryptoDash admin logo"
                    src="<?php echo e(asset('storage/logo/' . Session::get('logo'))); ?>" />
            </div>
        </a>
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="<?php echo e(active('dashboard')); ?> nav-item">
                <a href="/dashboard">
                    <i class="icon-grid"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="<?php echo e(active('', '/sop', '/coa')); ?> nav-item">
                <a href="#">
                    <i class="icon-support"></i>
                    <span class="menu-title">Pengaturan</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a class="menu-item" href="/pengaturan/sop">Personalisasi SOP</a>
                    </li>
                    <li>
                        <a class="menu-item" href="/pengaturan/coa">Cart Of Account</a>
                    </li>
                </ul>
            </li>
            <li class="<?php echo e(active('/jurnal_umum')); ?> nav-item">
                <a href="/transaksi/jurnal_umum">
                    <i class="icon-shuffle"></i>
                    <span class="menu-title">Jurnal Umum</span>
                </a>
            </li>
            <li class="<?php echo e(active('pelaporan')); ?> nav-item">
                <a href="/pelaporan">
                    <i class="icon-layers"></i>
                    <span class="menu-title">Laporan</span>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php /**PATH C:\laragon\www\demo\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>