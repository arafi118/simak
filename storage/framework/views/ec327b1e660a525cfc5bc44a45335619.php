<form action="/pengaturan/spk/<?php echo e($kec->id); ?>" method="post" id="FormSPK">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="my-3">
        <div id="editor">
            <ol>
                <?php echo json_decode($kec->redaksi_spk, true); ?>

            </ol>
        </div>
    </div>

    <textarea name="spk" id="spk" class="d-none"></textarea>
</form>

<div class="d-flex justify-content-end">
    <button type="button" id="SimpanSPK" data-target="#FormSPK" class="btn btn-sm btn-github mb-0 btn-simpan">
        Simpan Perubahan
    </button>
</div>
<?php /**PATH C:\laragon\www\demo\resources\views/sop/partials/_spk.blade.php ENDPATH**/ ?>