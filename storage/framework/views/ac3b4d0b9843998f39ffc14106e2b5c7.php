<form action="/pengaturan/berita_acara/<?php echo e($kec->id); ?>" method="post" id="BeritaAcara">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <div class="my-3">
        <div id="ba-editor"><?php echo json_decode($kec->berita_acara, true); ?></div>
    </div>

    <textarea name="ba" id="ba" class="d-none"></textarea>
</form>

<div class="d-flex justify-content-end">
    <button type="button" id="SimpanBeritaAcara" data-target="#BeritaAcara"
        class="btn btn-sm btn-github mb-0 btn-simpan">
        Simpan Perubahan
    </button>
</div>
<?php /**PATH C:\laragon\www\demo\resources\views/sop/partials/_berita_acara.blade.php ENDPATH**/ ?>