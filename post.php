<?php
require_once __DIR__ . '/includes/init.php';

$slug = $_GET['slug'] ?? '';
$post = $slug ? getPostBySlug($slug) : null;

if (!$post) {
    http_response_code(404);
    $pageTitle = siteTitle(__('error_post_not_found'));
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1>' . e(__('error_404')) . '</h1><p>' . e(__('no_results')) . '</p></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = siteTitle($post['title'] . ' · ' . __('blog_title'));
$pageDescription = truncate($post['excerpt'] ?? '', 160);
require_once __DIR__ . '/includes/header.php';

$comments = getComments((int)($post['id'] ?? 0));
$related = getRelatedPosts((int)($post['id'] ?? 0), isset($post['category_id']) ? (int)$post['category_id'] : null);
$tags = array_filter(array_map('trim', explode(',', $post['tags'] ?? '')));
?>

<section class="page-hero">
    <div class="container reveal">
        <div class="meta" style="color:var(--brand);font-weight:700"><?= e($post['category_name'] ?? __('blog_category_fallback')) ?></div>
        <h1><?= e($post['title']) ?></h1>
        <p><?= e(__('posted_by')) ?> <?= e($post['author'] ?? OWNER_NAME) ?> · <?= e(formatDate($post['created_at'] ?? date('Y-m-d'))) ?> · <?= (int)($post['views'] ?? 0) ?> <?= e(__('views')) ?></p>
    </div>
</section>

<section class="section">
    <div class="container blog-layout">
        <article>
            <div class="project-hero-media reveal">
                <img src="<?= e(mediaUrl($post['featured_image'] ?? null)) ?>" alt="<?= e($post['title']) ?>" loading="lazy" width="1200" height="675">
            </div>

            <?php if (!empty($post['video_url'])): ?>
            <div class="reveal" style="margin-bottom:1.5rem;border-radius:var(--radius);overflow:hidden;aspect-ratio:16/9;background:#000">
                <?php if (preg_match('/(youtube\.com|youtu\.be)/', $post['video_url'])): ?>
                    <iframe src="<?= e($post['video_url']) ?>" style="width:100%;height:100%;border:0" allowfullscreen loading="lazy" title="<?= e(__('video_demo')) ?>"></iframe>
                <?php else: ?>
                    <video src="<?= e(mediaUrl($post['video_url'])) ?>" controls playsinline style="width:100%;height:100%"></video>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="prose reveal">
                <?= $post['content'] ?? '' ?>
            </div>

            <?php if ($tags): ?>
            <div class="reveal" style="margin-top:1.5rem">
                <strong><?= e(__('tags')) ?>:</strong>
                <?php foreach ($tags as $t): ?>
                    <a class="tag" href="<?= url('blog.php?tag=' . urlencode($t)) ?>"><?= e($t) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="reveal" style="margin-top:3rem">
                <h2 style="font-family:var(--font-display);margin-bottom:1rem"><?= e(__('comments')) ?> (<?= count($comments) ?>)</h2>
                <?php if (!$comments): ?>
                    <p style="color:var(--text-muted)"><?= e(__('leave_comment')) ?></p>
                <?php endif; ?>
                <?php foreach ($comments as $c): ?>
                <div class="comment">
                    <strong><?= e($c['name']) ?></strong>
                    <time><?= e(formatDate($c['created_at'])) ?></time>
                    <p><?= e($c['comment']) ?></p>
                </div>
                <?php endforeach; ?>

                <h3 style="margin:2rem 0 1rem;font-family:var(--font-display)"><?= e(__('leave_comment')) ?></h3>
                <form class="form" method="post" action="<?= url('api/comment.php') ?>">
                    <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>">
                    <input type="hidden" name="redirect" value="<?= e(url('post.php?slug=' . urlencode($post['slug']))) ?>">
                    <div class="form-row">
                        <div class="field">
                            <label for="cname"><?= e(__('form_name')) ?></label>
                            <input id="cname" type="text" name="name" required>
                        </div>
                        <div class="field">
                            <label for="cemail"><?= e(__('form_email')) ?></label>
                            <input id="cemail" type="email" name="email" required>
                        </div>
                    </div>
                    <div class="field">
                        <label for="cmsg"><?= e(__('form_message')) ?></label>
                        <textarea id="cmsg" name="comment" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit"><?= e(__('form_send')) ?></button>
                </form>
            </div>

            <?php if ($related): ?>
            <div style="margin-top:3rem">
                <h2 style="font-family:var(--font-display);margin-bottom:1rem" class="reveal"><?= e(__('related_posts')) ?></h2>
                <div class="grid-2">
                    <?php foreach ($related as $r): ?>
                    <article class="card reveal">
                        <div class="card-body">
                            <h3><a href="<?= url('post.php?slug=' . urlencode($r['slug'])) ?>"><?= e($r['title']) ?></a></h3>
                            <p><?= e(truncate($r['excerpt'] ?? '', 90)) ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </article>

        <aside>
            <div class="sidebar-widget reveal">
                <h3><?= e(__('categories')) ?></h3>
                <?php foreach (getCategories('blog') as $cat): ?>
                <a href="<?= url('blog.php?category=' . urlencode($cat['slug'])) ?>"><?= e($cat['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </aside>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
