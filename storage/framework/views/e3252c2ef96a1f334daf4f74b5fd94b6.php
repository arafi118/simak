<?php $__currentLoopData = $parent_menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(count($menu->child) > 0): ?>
        <?php
            $link = [];
            $path = '/' . Request::path();

            $links = $menu->pluck('link');
            $links->each(function ($menu_link) {
                $link[] = $menu_link;
            });

            $active = '';
            if (in_array($path, $link)) {
                $active = 'active';
            }

            if (in_array(str_replace('#', '', $menu->link), explode('/', $path))) {
                $active = 'active';
            }

            foreach ($menu->child as $child) {
                if ($active == 'active') {
                    break;
                }

                $child_link = explode('/', $child);
                if (in_array(Request::path(), $child_link)) {
                    $active = 'active';
                }
            }
        ?>
        <li class="nav-item">
            <a data-bs-toggle="collapse" href="#menu_<?php echo e(str_replace('#', '', $menu->link)); ?>"
                class="nav-link text-white <?php echo e($active); ?>"
                aria-controls="menu_<?php echo e(str_replace('#', '', $menu->link)); ?>" role="button" aria-expanded="false">
                <?php if($menu->type == 'material'): ?>
                    <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="material-icons opacity-10"><?php echo e($menu->ikon); ?></i>
                    </div>
                <?php else: ?>
                    <span class="sidenav-mini-icon"> <?php echo e($menu->ikon); ?> </span>
                <?php endif; ?>
                <span class="nav-link-text ms-1"><?php echo e($menu->title); ?></span>
            </a>
            <div class="collapse" id="menu_<?php echo e(str_replace('#', '', $menu->link)); ?>">
                <ul class="nav nav-sm flex-column">
                    <?php echo $__env->make('layouts.menu', ['parent_menu' => $menu->child], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </ul>
            </div>
        </li>
    <?php else: ?>
        <?php if($menu->link == '' && $menu->type == '' && $menu->ikon == ''): ?>
            <li class="nav-item mt-3">
                <h6 class="ps-4  ms-2 text-uppercase text-xs font-weight-bolder text-white"><?php echo e($menu->title); ?></h6>
            </li>
        <?php else: ?>
            <?php
                $path = '/' . Request::path();

                $arr_path = explode('/', $path);
                $arr_menu = explode('/', $menu->link);

                $end_page = end($arr_path);
                $end_menu_link = end($arr_menu);

                $active = '';
                if ($path == $menu->link || $end_page == $end_menu_link) {
                    $active = 'active';
                }

                if ((in_array('detail', $arr_path) || in_array('lunas', $arr_path)) && $menu->link == '/perguliran') {
                    $active = 'active';
                }
            ?>
            <li class="nav-item nav-item-link <?php echo e($active); ?>">
                <a class="nav-link text-white <?php echo e($active); ?>" href="<?php echo e($menu->link); ?>">
                    <?php if($menu->type == 'material'): ?>
                        <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="material-icons opacity-10"><?php echo e($menu->ikon); ?></i>
                        </div>
                    <?php else: ?>
                        <span class="sidenav-mini-icon"> <?php echo e($menu->ikon); ?> </span>
                    <?php endif; ?>
                    <span class="nav-link-text ms-1"><?php echo e($menu->title); ?></span>
                </a>
            </li>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\laragon\www\demo\resources\views/layouts/menu.blade.php ENDPATH**/ ?>