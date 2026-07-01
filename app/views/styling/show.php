<section class="section section-styling-detail">
    <div class="styling-breadcrumb container">
        <a href="/">Home</a> / <a href="/">Styling</a> / <span><?= e($styling['title']) ?></span>
    </div>

    <?php if (!empty($styling['model_info'])): ?>
        <div class="styling-model-info container">
            <?= nl2br(e($styling['model_info'])) ?>
        </div>
    <?php endif; ?>

    <div class="styling-detail-track container">
        <?php foreach ($images as $img): ?>
            <figure class="styling-card">
                <img src="<?= e($img['image_url']) ?>" alt="<?= e($styling['title']) ?>">
            </figure>
        <?php endforeach; ?>
    </div>
</section>
