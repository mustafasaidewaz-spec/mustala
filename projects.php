<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('projects');
$pageDescription = __('projects_hero_sub');
$pageKeywords = 'projects, portfolio, web development, wordpress, ecommerce, mustala, mustafa saide';
require_once __DIR__ . '/includes/header.php';

$projectsDataUrl = url('api/projects.php');
?>

<link rel="stylesheet" href="<?= asset('css/projects.css') ?>">

<section class="page-hero projects-page">
    <div class="max-width reveal">
        <h1><?= e(__('my_projects')) ?></h1>
        <p><?= e(__('projects_hero_sub')) ?></p>
    </div>
</section>

<section class="section" style="background:#f7f7f7">
    <div class="max-width">
        <div id="projects-app"
             data-json="<?= e($projectsDataUrl) ?>"
             data-base="<?= e(BASE_URL) ?>"
             data-search-placeholder="<?= e(__('projects_search')) ?>"
             data-showing-label="<?= e(__('projects_showing')) ?>"
             data-count-one="<?= e(__('projects_count_one')) ?>"
             data-count-many="<?= e(__('projects_count_many')) ?>"
             data-loading="<?= e(__('projects_loading')) ?>"
             data-empty="<?= e(__('projects_empty')) ?>"
             data-load-error="<?= e(__('projects_load_error')) ?>"
             data-view-project="<?= e(__('view_project')) ?>"
             data-live-demo="<?= e(__('live_demo')) ?>"
             data-github="<?= e(__('view_github')) ?>"
             data-video-demo="<?= e(__('video_demo')) ?>"
             data-features="<?= e(__('project_features')) ?>"
             data-technologies="<?= e(__('tech_stack')) ?>"
             data-completed="<?= e(__('project_completed')) ?>"
             data-play-video="<?= e(__('aria_play_video')) ?>">

            <div class="projects-toolbar reveal">
                <div class="project-filters" data-filters aria-label="<?= e(__('aria_project_categories')) ?>"></div>
                <div class="projects-search">
                    <i class="fas fa-search" aria-hidden="true"></i>
                    <input type="search" data-search placeholder="<?= e(__('projects_search')) ?>" aria-label="<?= e(__('projects_search')) ?>">
                </div>
            </div>

            <p class="reveal" style="color:#666;margin:-.5rem 0 1.25rem;font-size:.92rem">
                <?= e(__('projects_showing')) ?> <strong data-count>0 <?= e(__('projects_count_many')) ?></strong>
            </p>

            <div class="projects-grid" data-grid>
                <div class="projects-empty"><?= e(__('projects_loading')) ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Project details modal -->
<div class="pj-modal" id="pj-details-modal" role="dialog" aria-modal="true" aria-labelledby="pj-details-title" hidden>
    <div class="pj-modal-backdrop" data-close-details></div>
    <div class="pj-modal-dialog">
        <button type="button" class="pj-modal-close" data-close-details aria-label="<?= e(__('aria_close')) ?>">&times;</button>
        <div class="pj-modal-body" id="pj-details-body"></div>
    </div>
</div>

<!-- Video modal -->
<div class="pj-modal" id="pj-video-modal" role="dialog" aria-modal="true" aria-label="<?= e(__('aria_project_video')) ?>" hidden>
    <div class="pj-modal-backdrop" data-close-video></div>
    <div class="pj-video-frame" id="pj-video-frame"></div>
    <button type="button" class="pj-modal-close pj-video-close" data-close-video aria-label="<?= e(__('aria_close_video')) ?>">&times;</button>
</div>

<!-- Image lightbox -->
<div class="pj-lightbox" id="pj-lightbox" role="dialog" aria-modal="true" aria-label="<?= e(__('aria_image_gallery')) ?>" hidden>
    <div class="pj-lightbox-backdrop" data-close-lightbox></div>
    <button type="button" class="pj-lightbox-close" data-close-lightbox aria-label="<?= e(__('aria_close')) ?>">&times;</button>
    <button type="button" class="pj-lightbox-nav prev" data-lightbox-prev aria-label="<?= e(__('aria_prev_image')) ?>"><i class="fas fa-chevron-left"></i></button>
    <button type="button" class="pj-lightbox-nav next" data-lightbox-next aria-label="<?= e(__('aria_next_image')) ?>"><i class="fas fa-chevron-right"></i></button>
    <figure class="pj-lightbox-figure">
        <img src="" alt="" id="pj-lightbox-img">
    </figure>
    <div class="pj-lightbox-caption" id="pj-lightbox-caption"></div>
</div>

<script>
  // Ensure modals are not stuck with hidden attribute when opened via class
  ['pj-details-modal','pj-video-modal','pj-lightbox'].forEach(function (id) {
    var el = document.getElementById(id);
    if (el) el.removeAttribute('hidden');
  });
</script>
<script src="<?= asset('js/projects.js') ?>" defer></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
