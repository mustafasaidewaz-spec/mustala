<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('gallery');
$pageDescription = __('gallery_sub');
require_once __DIR__ . '/includes/header.php';

$type = $_GET['type'] ?? null;
$category = $_GET['category'] ?? null;
$page = max(1, (int)($_GET['page'] ?? 1));
$result = getGallery($category ?: null, $type ?: null, $page);
$categories = getCategories('gallery');
$qs = [];
if ($type) $qs['type'] = $type;
if ($category) $qs['category'] = $category;
$baseUrl = 'gallery.php' . ($qs ? '?' . http_build_query($qs) : '');
?>

<section class="page-hero">
    <div class="container reveal">
        <h1><?= e(__('gallery_title')) ?></h1>
        <p><?= e(__('gallery_sub')) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="toolbar reveal">
            <div class="filters">
                <a class="<?= !$type && !$category ? 'active' : '' ?>" href="<?= url('gallery.php') ?>"><?= e(__('filter_all')) ?></a>
                <a class="<?= $type === 'image' ? 'active' : '' ?>" href="<?= url('gallery.php?type=image') ?>"><?= e(__('images')) ?></a>
                <a class="<?= $type === 'video' ? 'active' : '' ?>" href="<?= url('gallery.php?type=video') ?>"><?= e(__('videos')) ?></a>
                <?php foreach ($categories as $cat): ?>
                <a class="<?= $category === $cat['slug'] ? 'active' : '' ?>" href="<?= url('gallery.php?category=' . urlencode($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($result['items'])): ?>
            <div class="empty-state reveal">
                <p><?= e(__('no_results')) ?></p>
                <p style="margin-top:.75rem;font-size:.95rem"><?= e(__('gallery_empty_hint')) ?></p>
            </div>
        <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($result['items'] as $item): ?>
            <?php
                $src = mediaUrl($item['file_path']);
                $isVideo = ($item['media_type'] ?? '') === 'video';
            ?>
            <a class="gallery-item reveal" href="<?= e($src) ?>" data-lightbox="<?= $isVideo ? 'video' : 'image' ?>" title="<?= e($item['title']) ?>">
                <?php if ($isVideo): ?>
                    <video src="<?= e($src) ?>" muted preload="metadata"></video>
                    <div class="play"><i class="fas fa-play"></i></div>
                <?php else: ?>
                    <img src="<?= e($src) ?>" alt="<?= e($item['title']) ?>" loading="lazy" width="480" height="360">
                <?php endif; ?>
                <div class="overlay"><?= e($item['title']) ?></div>
            </a>
            <?php endforeach; ?>
        </div>
        <?= pagination($page, $result['pages'], url($baseUrl)) ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
