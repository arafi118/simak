<div class="row">
    <div class="col-md-6">
        <div class="card mt-4 border" data-animation="true">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                <a class="d-block blur-shadow-image">
                    <img src="<?php echo e(asset('storage/logo/' . Session::get('logo'))); ?>" alt="img-blur-shadow"
                        class="img-fluid shadow border-radius-lg" id="previewLogo">
                </a>
                <div class="colored-shadow"
                    style="background-image: url(&quot;<?php echo e(asset('storage/logo/' . Session::get('logo'))); ?>&quot;);">
                </div>
            </div>
            <div class="card-body text-center pb-0">
                <div class="d-flex mt-n6 justify-content-center">
                    <button class="btn btn-link text-info border-0" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-original-title="Edit" id="EditLogo">
                        <i class="material-icons text-lg">edit</i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="/pengaturan/logo/<?php echo e($kec->id); ?>" method="post" enctype="multipart/form-data" id="FormLogo">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <input type="file" name="logo_kec" id="logo_kec" class="d-none">
</form>
<?php /**PATH C:\laragon\www\demo\resources\views/sop/partials/_logo.blade.php ENDPATH**/ ?>