<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('blog');
$pageDescription = __('blog_sub');
require_once __DIR__ . '/includes/header.php';

$category = $_GET['category'] ?? null;
$tag = $_GET['tag'] ?? null;
$search = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$result = getPosts($category ?: null, $tag ?: null, $search ?: null, $page);
$categories = getCategories('blog');
$qs = [];
if ($category) $qs['category'] = $category;
if ($tag) $qs['tag'] = $tag;
if ($search !== '') $qs['q'] = $search;
$baseUrl = 'blog.php' . ($qs ? '?' . http_build_query($qs) : '');

$allTags = [];
foreach ($result['items'] as $p) {
    foreach (explode(',', $p['tags'] ?? '') as $t) {
        $t = trim($t);
        if ($t !== '') $allTags[$t] = true;
    }
}
?>

<section class="page-hero blog-hero">
    <div class="max-width reveal">
        <h1><?= e(__('blog_title')) ?></h1>
        <p><?= e(__('blog_sub')) ?></p>
    </div>
</section>

<section class="section blog-page">
    <div class="max-width">
        <div class="blog-toolbar reveal">
            <form class="search-box blog-search" method="get" action="<?= url('blog.php') ?>">
                <input type="search" name="q" placeholder="<?= e(__('search_placeholder')) ?>" value="<?= e($search) ?>">
                <button type="submit" aria-label="<?= e(__('aria_search')) ?>"><i class="fas fa-search"></i></button>
            </form>
            <div class="blog-filter-pills">
                <a class="<?= !$category && !$tag ? 'active' : '' ?>" href="<?= url('blog.php') ?>"><?= e(__('filter_all')) ?></a>
                <?php foreach ($categories as $cat): ?>
                <a class="<?= ($category === $cat['slug']) ? 'active' : '' ?>"
                   href="<?= url('blog.php?category=' . urlencode($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="blog-layout">
            <div class="blog-main">
                <?php if ($tag): ?>
                <p class="blog-active-filter reveal"><?= e(__('blog_tag_label')) ?> <strong><?= e($tag) ?></strong>
                    <a href="<?= url('blog.php') ?>"><?= e(__('blog_clear_filter')) ?></a>
                </p>
                <?php endif; ?>

                <?php if (empty($result['items'])): ?>
                <div class="empty-state reveal"><?= e(__('no_results')) ?></div>
                <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($result['items'] as $post): ?>
                    <article class="blog-card reveal">
                        <a class="blog-card-media" href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>">
                            <img src="<?= e(mediaUrl($post['featured_image'] ?? null)) ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                        </a>
                        <div class="blog-card-body">
                            <div class="blog-meta"><?= e($post['category_name'] ?? __('blog_category_fallback')) ?> · <?= e(formatDate($post['created_at'] ?? date('Y-m-d'))) ?></div>
                            <h3><a href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
                            <p><?= e(truncate($post['excerpt'] ?? '', 130)) ?></p>
                            <a class="btn btn-sm" href="<?= url('post.php?slug=' . urlencode($post['slug'])) ?>"><?= e(__('read_more')) ?></a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <div class="blog-pagination"><?= pagination($page, $result['pages'], url($baseUrl)) ?></div>
                <?php endif; ?>
            </div>

            <aside class="blog-sidebar">
                <div class="sidebar-widget reveal">
                    <h3><?= e(__('categories')) ?></h3>
                    <div class="sidebar-links">
                        <a class="<?= !$category ? 'active' : '' ?>" href="<?= url('blog.php') ?>"><?= e(__('filter_all')) ?></a>
                        <?php foreach ($categories as $cat): ?>
                        <a class="<?= ($category === $cat['slug']) ? 'active' : '' ?>"
                           href="<?= url('blog.php?category=' . urlencode($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php if ($allTags): ?>
                <div class="sidebar-widget reveal">
                    <h3><?= e(__('tags')) ?></h3>
                    <div class="tag-cloud">
                        <?php foreach (array_keys($allTags) as $t): ?>
                        <a class="tag<?= ($tag === $t) ? ' active' : '' ?>" href="<?= url('blog.php?tag=' . urlencode($t)) ?>"><?= e($t) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
