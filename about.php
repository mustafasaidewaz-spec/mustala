<?php
require_once __DIR__ . '/includes/init.php';
$bodyClass = 'about-page';
$pageTitle = sitePageTitle('about');
$pageDescription = __('about_bio');
require_once __DIR__ . '/includes/header.php';

$skills = getSkillBarItems();
$experience = getExperienceTimeline();
$education = getEducationTimeline();
$certificates = getCertificateList();
$tech = ['HTML5', 'CSS3', 'JavaScript', 'PHP', 'MySQL', 'WordPress', 'WooCommerce', 'SEO', 'Git', 'Figma'];
$aboutImg = file_exists(__DIR__ . '/img/about im.jpeg') ? url('img/about%20im.jpeg') : asset('img/placeholder.svg');
$cv = getSetting('cv_path', asset('uploads/Mustafa_Saide_CV.pdf'));
?>

<section class="page-hero about-hero">
    <div class="max-width reveal">
        <h1><?= e(__('about_title')) ?></h1>
        <p><?= e(__('about_intro')) ?></p>
    </div>
</section>

<section class="section about-intro" id="about">
    <div class="max-width about-layout">
        <div class="about-photo reveal">
            <img src="<?= e($aboutImg) ?>" alt="<?= e(OWNER_NAME) ?>" loading="lazy" width="480" height="480">
        </div>
        <div class="about-intro-content reveal">
            <h2 class="about-headline">
                <?= e(__('about_typing')) ?> <span class="typing-2"></span>
            </h2>
            <p class="about-bio prose"><?= e(__('about_bio')) ?></p>
            <div class="btn-group about-actions">
                <a class="btn" href="<?= e($cv) ?>" download><?= e(__('cta_cv')) ?></a>
                <a class="btn btn-outline" href="<?= url('contact.php') ?>"><?= e(__('cta_hire')) ?></a>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt about-resume" id="experience">
    <div class="max-width grid-2 about-grid">
        <div class="reveal">
            <h2 class="about-section-title"><?= e(__('experience')) ?></h2>
            <div class="timeline">
                <?php foreach ($experience as $item): ?>
                <div class="timeline-item">
                    <div class="year"><?= e($item['year']) ?></div>
                    <h4><?= e($item['title']) ?></h4>
                    <p><?= e($item['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="reveal">
            <h2 class="about-section-title"><?= e(__('education')) ?></h2>
            <div class="timeline">
                <?php foreach ($education as $item): ?>
                <div class="timeline-item">
                    <div class="year"><?= e($item['year']) ?></div>
                    <h4><?= e($item['title']) ?></h4>
                    <p><?= e($item['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section about-skills" id="skills">
    <div class="max-width grid-2 about-grid">
        <div class="reveal">
            <h2 class="about-section-title"><?= e(__('skills')) ?></h2>
            <div class="skill-bars">
                <?php foreach ($skills as $skill): ?>
                <div class="skill-bar-item">
                    <div class="info"><span><?= e($skill['name']) ?></span><span><?= (int)$skill['level'] ?>%</span></div>
                    <div class="bar"><span data-width="<?= (int)$skill['level'] ?>%"></span></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="about-skills-side">
            <div class="reveal about-side-block">
                <h2 class="about-section-title"><?= e(__('technologies')) ?></h2>
                <div class="tech-cloud">
                    <?php foreach ($tech as $t): ?>
                        <span><?= e($t) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="reveal about-side-block">
                <h2 class="about-section-title"><?= e(__('certificates')) ?></h2>
                <ul class="cert-list">
                    <?php foreach ($certificates as $c): ?>
                    <li><i class="fas fa-certificate" aria-hidden="true"></i><span><?= e($c) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="cta-band about-cta">
    <div class="max-width reveal">
        <h2><?= e(__('contact_cta_title')) ?></h2>
        <p><?= e(__('contact_cta_sub')) ?></p>
        <a class="btn btn-primary" href="<?= url('contact.php') ?>"><?= e(__('cta_contact')) ?></a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
