<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('faq');
$pageDescription = __('faq_sub');
require_once __DIR__ . '/includes/header.php';

$faqs = getFaqItems();
?>

<section class="page-hero">
    <div class="max-width reveal">
        <h1><?= e(__('faq_title')) ?></h1>
        <p><?= e(__('faq_sub')) ?></p>
    </div>
</section>

<section class="section">
    <div class="max-width" style="max-width:820px">
        <div class="accordion">
            <?php foreach ($faqs as $faq): ?>
            <div class="accordion-item reveal">
                <button type="button" class="accordion-btn" aria-expanded="false">
                    <?= e($faq['q']) ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="accordion-panel">
                    <div class="accordion-panel-inner"><?= e($faq['a']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:2.5rem" class="reveal">
            <a class="btn" href="<?= url('contact.php') ?>"><?= e(__('cta_contact')) ?></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
