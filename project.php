<?php
require_once __DIR__ . '/includes/init.php';

$slug = $_GET['slug'] ?? '';
$project = $slug ? getProjectBySlug($slug) : null;

if (!$project) {
    http_response_code(404);
    $pageTitle = siteTitle(__('error_project_not_found'));
    require_once __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="max-width"><h1>' . e(__('error_404')) . '</h1><p>' . e(__('no_results')) . '</p><a class="btn" href="' . url('projects.php') . '">' . e(__('nav_portfolio')) . '</a></div></section>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$bodyClass = 'project-page';
$pageTitle = siteTitle($project['title']);
$pageDescription = truncate(strip_tags($project['description'] ?? ''), 160);
$pageImage = mediaUrl($project['featured_image'] ?? null, 'img/og-default.svg');
require_once __DIR__ . '/includes/header.php';

$images = $project['images'] ?? [];
$techStack = array_values(array_filter(array_map('trim', explode(',', (string)($project['tech_stack'] ?? '')))));
$features = projectFeatureList($project['content'] ?? '');
$contentHtml = trim((string)($project['content'] ?? ''));
$youtubeEmbed = projectYoutubeEmbed($project['video_url'] ?? null);
$videoFile = '';
if (!$youtubeEmbed && !empty($project['video_url'])) {
    $videos = parseProjectVideos($project['video_url']);
    if (($videos[0]['type'] ?? '') === 'file') {
        $videoFile = mediaUrl($videos[0]['src']);
    }
}
$hasLive = projectLinkIsValid($project['live_demo'] ?? null);
$hasGithub = projectLinkIsValid($project['github_url'] ?? null);
$hasVideo = $youtubeEmbed || $videoFile !== '';
$completedAt = !empty($project['created_at']) ? formatDate($project['created_at'], 'M Y') : '';
$categoryLabel = strtoupper((string)($project['category_name'] ?? ''));
$featuredPath = (string)($project['featured_image'] ?? '');
$featuredUrl = isImageMediaPath($featuredPath) ? mediaUrl($featuredPath) : asset('img/placeholder.svg');
$thumbImages = projectUniqueGalleryImages($images, $featuredPath);
$hasThumbs = count($thumbImages) > 1;
$galleryUrls = array_values(array_map(static function ($img) {
    return mediaUrl($img['image_path'] ?? null);
}, $thumbImages));
?>

<link rel="stylesheet" href="<?= asset('css/projects.css') ?>">

<section class="page-hero project-detail-hero">
    <div class="max-width">
        <div class="project-detail-topbar reveal">
            <a class="project-detail-back" href="<?= url('projects.php') ?>">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> <?= e(__('project_back')) ?>
            </a>
            <?php if ($categoryLabel !== ''): ?>
            <span class="project-detail-topbar-sep" aria-hidden="true">·</span>
            <span class="project-detail-topbar-cat"><?= e($categoryLabel) ?></span>
            <?php endif; ?>
        </div>
        <div class="project-detail-hero-copy reveal">
            <h1><?= e($project['title']) ?></h1>
            <?php if (!empty($project['description'])): ?>
            <p><?= e($project['description']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="project-detail-body">
    <div class="max-width">
        <div class="project-detail-showcase reveal" data-project-gallery="<?= e(json_encode($galleryUrls, JSON_UNESCAPED_SLASHES)) ?>">
            <div class="project-browser">
                <div class="project-browser-bar" aria-hidden="true">
                    <span></span><span></span><span></span>
                    <div class="project-browser-url"></div>
                </div>
                <div class="project-browser-stage">
                    <a class="project-browser-viewport" id="project-main-link" href="<?= e($featuredUrl) ?>" title="<?= e(__('view_full_image')) ?>">
                        <img id="project-main-image" src="<?= e($featuredUrl) ?>" alt="<?= e($project['title']) ?>" loading="eager" width="960" height="540">
                        <span class="project-media-overlay">
                            <i class="fas fa-search-plus" aria-hidden="true"></i>
                            <?= e(__('view_full_image')) ?>
                        </span>
                    </a>
                </div>
            </div>
            <?php if ($hasThumbs): ?>
            <div class="project-detail-thumbs" role="tablist" aria-label="<?= e(__('gallery_title')) ?>">
                <?php foreach ($thumbImages as $index => $img): ?>
                <?php $shot = mediaUrl($img['image_path'] ?? null); ?>
                <button type="button"
                    class="project-detail-thumb<?= $index === 0 ? ' is-active' : '' ?>"
                    role="tab"
                    aria-selected="<?= $index === 0 ? 'true' : 'false' ?>"
                    aria-label="<?= e($project['title']) ?> — <?= e(__('gallery_title')) ?> <?= (int)$index + 1 ?>"
                    data-src="<?= e($shot) ?>">
                    <img src="<?= e($shot) ?>" alt="" loading="lazy" width="120" height="75">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="project-detail-layout">
            <div class="project-detail-main reveal">
                <div class="project-detail-main-card">
                <?php if ($contentHtml !== ''): ?>
                <div class="project-detail-prose">
                    <?= $contentHtml ?>
                </div>
                <?php elseif (!empty($project['description'])): ?>
                <div class="project-detail-prose">
                    <p><?= e($project['description']) ?></p>
                </div>
                <?php endif; ?>

                <?php if ($features): ?>
                <div class="project-detail-features">
                    <h2><?= e(__('project_features')) ?></h2>
                    <ul>
                        <?php foreach ($features as $feature): ?>
                        <li><?= e($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
                </div>
            </div>

            <aside class="project-detail-sidebar reveal">
                <div class="project-detail-card">
                    <h3><?= e(__('tech_stack')) ?></h3>
                    <?php if ($techStack): ?>
                    <div class="project-detail-tech">
                        <?php foreach ($techStack as $tech): ?>
                        <span><?= e($tech) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="project-detail-muted">—</p>
                    <?php endif; ?>

                    <?php if ($completedAt !== ''): ?>
                    <div class="project-detail-completed">
                        <span><?= e(__('project_completed')) ?></span>
                        <strong><?= e($completedAt) ?></strong>
                    </div>
                    <?php endif; ?>

                    <?php if ($hasLive || $hasGithub || $hasVideo): ?>
                    <div class="project-detail-actions">
                        <?php if ($hasLive): ?>
                        <a class="btn project-detail-btn-primary" href="<?= e($project['live_demo']) ?>" target="_blank" rel="noopener">
                            <i class="fas fa-external-link-alt" aria-hidden="true"></i> <?= e(__('live_demo')) ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($hasGithub): ?>
                        <a class="btn project-detail-btn-outline" href="<?= e($project['github_url']) ?>" target="_blank" rel="noopener">
                            <i class="fab fa-github" aria-hidden="true"></i> <?= e(__('view_github')) ?>
                        </a>
                        <?php endif; ?>
                        <?php if ($hasVideo): ?>
                        <a class="btn <?= ($hasLive || $hasGithub) ? 'project-detail-btn-outline' : 'project-detail-btn-outline project-detail-btn-video' ?>" href="#project-video">
                            <i class="fas fa-play" aria-hidden="true"></i> <?= e(__('video_demo')) ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>

        <?php if ($youtubeEmbed): ?>
        <div class="project-detail-block project-detail-block--video reveal" id="project-video">
            <h2 class="project-detail-section-title"><?= e(__('video_demo')) ?></h2>
            <div class="project-detail-media-frame">
                <iframe src="<?= e($youtubeEmbed) ?>" title="<?= e($project['title'] . ' — ' . __('video_demo')) ?>" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
            </div>
        </div>
        <?php elseif ($videoFile !== ''): ?>
        <div class="project-detail-block project-detail-block--video reveal" id="project-video">
            <h2 class="project-detail-section-title"><?= e(__('video_demo')) ?></h2>
            <div class="project-detail-media-frame">
                <video controls playsinline preload="metadata" poster="<?= e(mediaUrl($project['featured_image'] ?? null)) ?>">
                    <source src="<?= e($videoFile) ?>" type="video/mp4">
                </video>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="cta-band project-detail-cta">
    <div class="max-width reveal">
        <h2><?= e(__('project_cta_title')) ?></h2>
        <p><?= e(__('project_cta_sub')) ?></p>
        <a class="btn btn-primary" href="<?= url('contact.php') ?>"><?= e(__('cta_contact')) ?></a>
    </div>
</section>

<script src="<?= asset('js/project-detail.js') ?>" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
