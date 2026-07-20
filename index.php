<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = __('meta_home_title');
$pageDescription = __('meta_home_desc');
require_once __DIR__ . '/includes/header.php';

$testimonials = getTestimonials(3);
$aboutImg = file_exists(__DIR__ . '/img/about im.jpeg') ? url('img/about%20im.jpeg') : asset('img/placeholder.svg');
$team2 = file_exists(__DIR__ . '/img/team 2.jpeg') ? url('img/team%202.jpeg') : $aboutImg;
$team3 = file_exists(__DIR__ . '/img/team 3 - Copy.jpeg') ? url('img/team%203%20-%20Copy.jpeg') : $aboutImg;
$cv = getSetting('cv_path', asset('uploads/Mustafa_Saide_CV.pdf'));

$teams = getTeamMembers($aboutImg, $team2, $team3);

$services = [
    ['icon' => 'fas fa-code', 'title' => __('svc_frontend'), 'desc' => __('svc_frontend_desc')],
    ['icon' => 'fas fa-shopping-cart', 'title' => __('svc_ecommerce'), 'desc' => __('svc_ecommerce_desc')],
    ['icon' => 'fab fa-wordpress', 'title' => __('svc_wordpress'), 'desc' => __('svc_wordpress_desc')],
];

$skills = getSkillBarItems();
$resume = getHomeResumeItems();
$featuredProjects = getFeaturedProjects(3);

$stats = [
    ['value' => 25, 'suffix' => '+', 'label' => __('stat_projects')],
    ['value' => 15, 'suffix' => '+', 'label' => __('stat_clients')],
    ['value' => 3, 'suffix' => '+', 'label' => __('stat_years')],
    ['value' => 100, 'suffix' => '%', 'label' => __('stat_support')],
];
?>

<section class="home" id="home">
    <div class="max-width">
        <div class="home-content reveal">
            <div class="text-1"><?= e(__('hero_hello')) ?></div>
            <div class="text-2"><?= e(OWNER_NAME) ?></div>
            <div class="text-3"><?= e(__('hero_typing')) ?> <span class="typing"></span></div>
            <p class="hero-lead"><?= e(__('hero_subtitle')) ?></p>
            <div class="btn-group">
                <a class="btn" href="#contact"><?= e(__('cta_hire')) ?></a>
            </div>
        </div>
    </div>
</section>

<section class="stats">
    <div class="max-width">
        <div class="stats-grid">
            <?php foreach ($stats as $stat): ?>
            <div class="stat-item reveal">
                <strong><span class="counter" data-target="<?= (int)$stat['value'] ?>">0</span><?= e($stat['suffix']) ?></strong>
                <span><?= e($stat['label']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="about" id="about">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_about_sub')) ?>"><?= e(__('about_title')) ?></h2>
        <div class="about-content">
            <div class="column left reveal">
                <img src="<?= e($aboutImg) ?>" alt="<?= e(OWNER_NAME) ?>" loading="lazy">
            </div>
            <div class="column right reveal">
                <div class="text"><?= e(__('about_typing')) ?> <span class="typing-2"></span></div>
                <p><?= e(__('about_bio')) ?></p>
                <div class="info-list">
                    <div class="info-row"><i class="fas fa-user"></i><div><span><?= e(__('label_name')) ?></span><strong><?= e(OWNER_NAME) ?></strong></div></div>
                    <div class="info-row"><i class="fas fa-map-marker-alt"></i><div><span><?= e(__('label_address')) ?></span><strong><?= e(OWNER_LOCATION) ?></strong></div></div>
                    <div class="info-row"><i class="fas fa-envelope"></i><div><span><?= e(__('label_email')) ?></span><strong><a href="mailto:<?= e(OWNER_EMAIL) ?>"><?= e(OWNER_EMAIL) ?></a></strong></div></div>
                    <div class="info-row"><i class="fab fa-whatsapp"></i><div><span><?= e(__('label_whatsapp')) ?></span><strong><a href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener">+<?= e(OWNER_WHATSAPP) ?></a></strong></div></div>
                </div>
                <div class="btn-group">
                    <a class="btn" href="<?= e($cv) ?>" download><?= e(__('cta_cv')) ?></a>
                    <a class="btn btn-outline" href="#contact"><?= e(__('cta_hire')) ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="services" id="services">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_services_sub')) ?>"><?= e(__('my_services')) ?></h2>
        <div class="serv-content">
            <?php foreach ($services as $s): ?>
            <div class="card reveal">
                <div class="box">
                    <i class="<?= e($s['icon']) ?>"></i>
                    <div class="text"><?= e($s['title']) ?></div>
                    <p><?= e($s['desc']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-actions">
            <a class="btn" href="<?= url('services.php') ?>"><?= e(__('view_all')) ?></a>
            <a class="btn btn-outline" href="#contact"><?= e(__('cta_hire')) ?></a>
        </div>
    </div>
</section>

<section class="projects-home" id="projects">
    <div class="max-width">
        <div class="projects-home-head">
            <h2><?= e(__('nav_portfolio')) ?></h2>
            <a class="view-all-link" href="<?= url('projects.php') ?>">
                <?= e(__('view_all')) ?> <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </a>
        </div>
        <div class="projects-home-grid">
            <?php foreach ($featuredProjects as $project): ?>
            <?php
                $projectUrl = url('project.php?slug=' . urlencode($project['slug']));
                $techItems = array_slice(array_values(array_filter(array_map('trim', explode(',', (string)($project['tech_stack'] ?? ''))))), 0, 3);
            ?>
            <article class="project-home-card reveal">
                <a class="project-home-media" href="<?= e($projectUrl) ?>">
                    <img src="<?= e(mediaUrl($project['featured_image'] ?? null)) ?>" alt="<?= e($project['title']) ?>" loading="lazy" width="640" height="400">
                    <?php if (!empty($project['category_name'])): ?>
                    <span class="project-home-cat"><?= e($project['category_name']) ?></span>
                    <?php endif; ?>
                </a>
                <div class="project-home-body">
                    <h3><a href="<?= e($projectUrl) ?>"><?= e($project['title']) ?></a></h3>
                    <p><?= e(truncate(strip_tags((string)($project['description'] ?? '')), 120)) ?></p>
                    <?php if ($techItems): ?>
                    <div class="project-home-tech">
                        <?php foreach ($techItems as $tech): ?>
                        <span><?= e($tech) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div class="project-home-actions">
                        <a class="btn btn-sm" href="<?= e($projectUrl) ?>"><?= e(__('view_project')) ?></a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="skills" id="skills">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_skills_sub')) ?>"><?= e(__('my_skills')) ?></h2>
        <div class="skills-content">
            <div class="column left reveal">
                <div class="text"><?= e(__('skills_creative')) ?></div>
                <p><?= e(__('skills_intro')) ?></p>
                <a class="btn" href="<?= url('about.php#skills') ?>"><?= e(__('read_more')) ?></a>
            </div>
            <div class="column right reveal">
                <?php foreach ($skills as $skill): ?>
                <div class="bars">
                    <div class="info">
                        <span><?= e($skill['name']) ?></span>
                        <span><?= (int)$skill['level'] ?>%</span>
                    </div>
                    <div class="line" style="--bar: <?= (int)$skill['level'] ?>%"></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="resume" id="resume">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_resume_sub')) ?>"><?= e(__('my_resume')) ?></h2>
        <div class="resume-list">
            <?php foreach ($resume as $item): ?>
            <div class="resume-item reveal">
                <div class="resume-period"><?= e($item['period']) ?></div>
                <div class="resume-body">
                    <h3><?= e($item['title']) ?></h3>
                    <div class="resume-place"><?= e($item['place']) ?></div>
                    <p><?= e($item['desc']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="teams" id="teams">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_teams_sub')) ?>"><?= e(__('my_teams')) ?></h2>
        <div class="teams-slider" data-teams-slider>
            <div class="teams-viewport">
                <div class="teams-grid" data-teams-track>
                    <?php foreach ($teams as $member): ?>
                    <article class="team-card">
                        <div class="box">
                            <img src="<?= e($member['image']) ?>" alt="<?= e($member['name']) ?>" loading="lazy">
                            <div class="text"><?= e($member['name']) ?></div>
                            <p><?= e($member['bio']) ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="teams-dots" data-teams-dots role="tablist" aria-label="<?= e(__('aria_team_slides')) ?>"></div>
        </div>
    </div>
</section>

<section class="testimonials-home" id="testimonials">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_testimonials_sub')) ?>"><?= e(__('testimonials_preview')) ?></h2>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $t): ?>
            <div class="testimonial reveal">
                <div class="stars"><?= stars((int)$t['rating']) ?></div>
                <blockquote>“<?= e($t['content']) ?>”</blockquote>
                <div class="testimonial-author">
                    <div class="avatar"><?= e(mb_substr($t['client_name'], 0, 1)) ?></div>
                    <div>
                        <strong><?= e($t['client_name']) ?></strong>
                        <span><?= e(trim(($t['role'] ?? '') . (!empty($t['company']) ? ' · ' . $t['company'] : ''))) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="section-actions">
            <a class="btn btn-outline" href="<?= url('testimonials.php') ?>"><?= e(__('view_all')) ?></a>
        </div>
    </div>
</section>

<section class="contact" id="contact">
    <div class="max-width">
        <h2 class="title" data-subtitle="<?= e(__('home_contact_sub')) ?>"><?= e(__('my_contact')) ?></h2>
        <div class="contact-content">
            <div class="column left reveal">
                <div class="text"><?= e(__('get_in_touch')) ?></div>
                <p><?= e(__('contact_intro')) ?></p>
                <div class="icons">
                    <div class="row">
                        <i class="fas fa-user"></i>
                        <div class="info">
                            <div class="head"><?= e(__('label_name')) ?></div>
                            <div class="sub-title"><?= e(OWNER_NAME) ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <i class="fas fa-map-marker-alt"></i>
                        <div class="info">
                            <div class="head"><?= e(__('label_address')) ?></div>
                            <div class="sub-title"><?= e(OWNER_LOCATION) ?></div>
                        </div>
                    </div>
                    <div class="row">
                        <i class="fas fa-envelope"></i>
                        <div class="info">
                            <div class="head"><?= e(__('label_email')) ?></div>
                            <div class="sub-title"><a href="mailto:<?= e(OWNER_EMAIL) ?>"><?= e(OWNER_EMAIL) ?></a></div>
                        </div>
                    </div>
                    <div class="row">
                        <i class="fab fa-whatsapp"></i>
                        <div class="info">
                            <div class="head"><?= e(__('label_whatsapp')) ?></div>
                            <div class="sub-title"><a href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener">+<?= e(OWNER_WHATSAPP) ?></a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="column right reveal">
                <div class="text"><?= e(__('message_me')) ?></div>
                <form id="contact-form" method="post" action="<?= url('api/contact.php') ?>">
                    <div class="fields">
                        <div class="field name">
                            <input type="text" name="name" placeholder="<?= e(__('form_name')) ?>" required autocomplete="name">
                        </div>
                        <div class="field email">
                            <input type="email" name="email" placeholder="<?= e(__('form_email')) ?>" required autocomplete="email">
                        </div>
                    </div>
                    <div class="field">
                        <input type="text" name="subject" placeholder="<?= e(__('form_subject')) ?>" required>
                    </div>
                    <div class="field textarea">
                        <textarea name="message" cols="30" rows="8" placeholder="<?= e(__('form_message')) ?>" required></textarea>
                    </div>
                    <div class="button">
                        <button type="submit"><?= e(__('form_send')) ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<a class="whatsapp-float" href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_whatsapp')) ?>">
    <i class="fab fa-whatsapp"></i>
</a>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
