<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('services');
$pageDescription = __('services_sub');
require_once __DIR__ . '/includes/header.php';

$services = [
    ['icon' => 'fas fa-code', 'title' => __('svc_frontend'), 'desc' => __('svc_frontend_desc')],
    ['icon' => 'fas fa-shopping-cart', 'title' => __('svc_ecommerce'), 'desc' => __('svc_ecommerce_desc')],
    ['icon' => 'fab fa-wordpress', 'title' => __('svc_wordpress'), 'desc' => __('svc_wordpress_desc')],
    ['icon' => 'fas fa-rocket', 'title' => __('svc_landing'), 'desc' => __('svc_landing_desc')],
    ['icon' => 'fas fa-paint-brush', 'title' => __('svc_redesign'), 'desc' => __('svc_redesign_desc')],
    ['icon' => 'fas fa-screwdriver-wrench', 'title' => __('svc_maintenance'), 'desc' => __('svc_maintenance_desc')],
];

$process = [
    ['icon' => 'fas fa-comments', 'title' => __('svc_step_discover'), 'desc' => __('svc_step_discover_desc')],
    ['icon' => 'fas fa-pencil-ruler', 'title' => __('svc_step_design'), 'desc' => __('svc_step_design_desc')],
    ['icon' => 'fas fa-laptop-code', 'title' => __('svc_step_build'), 'desc' => __('svc_step_build_desc')],
    ['icon' => 'fas fa-rocket', 'title' => __('svc_step_launch'), 'desc' => __('svc_step_launch_desc')],
];
?>

<section class="page-hero services-hero">
    <div class="max-width reveal">
        <h1><?= e(__('services_title')) ?></h1>
        <p><?= e(__('services_sub')) ?></p>
    </div>
</section>

<section class="section services-page">
    <div class="max-width">
        <div class="services-page-head reveal">
            <h2><?= e(__('my_services')) ?></h2>
            <p><?= e(__('services_preview_sub')) ?></p>
        </div>
        <div class="services-page-grid">
            <?php foreach ($services as $i => $s): ?>
            <article class="service-card reveal">
                <span class="service-card-num"><?= sprintf('%02d', $i + 1) ?></span>
                <div class="service-card-icon" aria-hidden="true">
                    <i class="<?= e($s['icon']) ?>"></i>
                </div>
                <h3><?= e($s['title']) ?></h3>
                <p><?= e($s['desc']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt services-process">
    <div class="max-width">
        <div class="section-head reveal">
            <div>
                <h2><?= e(__('svc_process_title')) ?></h2>
                <p><?= e(__('svc_process_sub')) ?></p>
            </div>
        </div>
        <div class="process-grid">
            <?php foreach ($process as $i => $step): ?>
            <div class="process-step reveal">
                <div class="process-step-icon">
                    <i class="<?= e($step['icon']) ?>"></i>
                    <span class="process-step-num"><?= $i + 1 ?></span>
                </div>
                <h3><?= e($step['title']) ?></h3>
                <p><?= e($step['desc']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band services-cta">
    <div class="max-width reveal">
        <h2><?= e(__('svc_cta_title')) ?></h2>
        <p><?= e(__('svc_cta_sub')) ?></p>
        <div class="services-cta-actions">
            <a class="btn btn-primary" href="<?= url('pricing.php') ?>"><?= e(__('nav_pricing')) ?></a>
            <a class="btn btn-outline-light" href="<?= url('contact.php') ?>"><?= e(__('cta_hire')) ?></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
