<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('contact');
$pageDescription = __('contact_sub');
require_once __DIR__ . '/includes/header.php';
$plan = trim($_GET['plan'] ?? '');
$mapEmbed = getSetting('map_embed', '');
?>

<section class="page-hero contact-hero">
    <div class="max-width reveal">
        <span class="contact-badge"><?= e(__('available_badge')) ?></span>
        <h1><?= e(__('contact_title')) ?></h1>
        <p><?= e(__('contact_sub')) ?></p>
    </div>
</section>

<section class="section contact-page">
    <div class="max-width">
        <div class="contact-quick-actions reveal">
            <a class="contact-quick-card" href="mailto:<?= e(OWNER_EMAIL) ?>">
                <i class="fas fa-envelope"></i>
                <span><?= e(__('contact_quick_email')) ?></span>
                <small><?= e(OWNER_EMAIL) ?></small>
            </a>
            <a class="contact-quick-card" href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener">
                <i class="fab fa-whatsapp"></i>
                <span><?= e(__('contact_quick_wa')) ?></span>
                <small>+<?= e(OWNER_WHATSAPP) ?></small>
            </a>
            <div class="contact-quick-card contact-quick-card--static">
                <i class="fas fa-clock"></i>
                <span><?= e(__('contact_response_label')) ?></span>
                <small><?= e(__('contact_response_time')) ?></small>
            </div>
        </div>

        <div class="contact-page-grid">
            <div class="contact-info-panel reveal">
                <h2><?= e(__('get_in_touch')) ?></h2>
                <p class="contact-lead"><?= e(__('contact_intro')) ?></p>
                <ul class="contact-details">
                    <li>
                        <span class="contact-icon"><i class="fas fa-user"></i></span>
                        <div>
                            <strong><?= e(__('label_name')) ?></strong>
                            <span><?= e(OWNER_NAME) ?></span>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <div>
                            <strong><?= e(__('label_address')) ?></strong>
                            <span><?= e(OWNER_LOCATION) ?></span>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                        <div>
                            <strong><?= e(__('label_email')) ?></strong>
                            <a href="mailto:<?= e(OWNER_EMAIL) ?>"><?= e(OWNER_EMAIL) ?></a>
                        </div>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fab fa-whatsapp"></i></span>
                        <div>
                            <strong><?= e(__('label_whatsapp')) ?></strong>
                            <a href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener">+<?= e(OWNER_WHATSAPP) ?></a>
                        </div>
                    </li>
                </ul>
                <a class="btn btn-outline contact-wa-btn" href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener">
                    <i class="fab fa-whatsapp"></i> <?= e(__('contact_chat_wa')) ?>
                </a>
            </div>

            <div class="contact-form-panel reveal">
                <h2><?= e(__('message_me')) ?></h2>
                <p class="contact-form-note"><?= e(__('contact_form_note')) ?></p>
                <form id="contact-form" class="contact-form" method="post" action="<?= url('api/contact.php') ?>" novalidate>
                    <div class="form-row-two">
                        <div class="field">
                            <label for="contact-name"><?= e(__('form_name')) ?></label>
                            <input id="contact-name" type="text" name="name" placeholder="<?= e(__('form_placeholder_name')) ?>" required autocomplete="name">
                        </div>
                        <div class="field">
                            <label for="contact-email"><?= e(__('form_email')) ?></label>
                            <input id="contact-email" type="email" name="email" placeholder="<?= e(__('form_placeholder_email')) ?>" required autocomplete="email">
                        </div>
                    </div>
                    <div class="field">
                        <label for="contact-subject"><?= e(__('form_subject')) ?></label>
                        <input id="contact-subject" type="text" name="subject" placeholder="<?= e(__('form_placeholder_subject')) ?>"
                               value="<?= e($plan ? __('pricing_plan_prefix') . $plan : '') ?>" required>
                    </div>
                    <div class="field">
                        <label for="contact-message"><?= e(__('form_message')) ?></label>
                        <textarea id="contact-message" name="message" rows="6" placeholder="<?= e(__('form_placeholder_message')) ?>" required></textarea>
                    </div>
                    <button class="btn contact-submit" type="submit" data-label="<?= e(__('form_send')) ?>" data-sending="<?= e(__('contact_sending')) ?>">
                        <span class="contact-submit-text"><?= e(__('form_send')) ?></span>
                    </button>
                </form>
            </div>
        </div>

        <?php if ($mapEmbed !== ''): ?>
        <div class="contact-map reveal">
            <h2><?= e(__('contact_map_title')) ?></h2>
            <p><?= e(OWNER_LOCATION) ?></p>
            <div class="map-frame">
                <iframe src="<?= e($mapEmbed) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="<?= e(__('contact_map_title')) ?>"></iframe>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<a class="whatsapp-float" href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_whatsapp')) ?>">
    <i class="fab fa-whatsapp"></i>
</a>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
