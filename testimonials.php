<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('testimonials');
$pageDescription = __('testimonials_sub');
require_once __DIR__ . '/includes/header.php';
$testimonials = getTestimonials(20);
?>

<section class="page-hero">
    <div class="container reveal">
        <h1><?= e(__('testimonials_title')) ?></h1>
        <p><?= e(__('testimonials_sub')) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="grid-3">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial reveal" style="background:var(--bg-elevated);border:1px solid var(--border)">
                <div class="stars"><?= stars((int)$t['rating']) ?></div>
                <blockquote>“<?= e($t['content']) ?>”</blockquote>
                <div class="testimonial-author">
                    <?php if (!empty($t['client_image'])): ?>
                        <img class="avatar" src="<?= e(mediaUrl($t['client_image'])) ?>" alt="<?= e($t['client_name']) ?>" loading="lazy" width="48" height="48">
                    <?php else: ?>
                        <div class="avatar"><?= e(mb_substr($t['client_name'], 0, 1)) ?></div>
                    <?php endif; ?>
                    <div>
                        <strong><?= e($t['client_name']) ?></strong>
                        <span><?= e(trim(($t['role'] ?? '') . ($t['company'] ? ' · ' . $t['company'] : ''))) ?></span>
                    </div>
                </div>
                <?php if (!empty($t['company_logo'])): ?>
                    <img class="company-logo" src="<?= e(mediaUrl($t['company_logo'])) ?>" alt="<?= e($t['company'] ?? '') ?>" loading="lazy">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
