</main>

    <footer class="site-footer">
        <div class="max-width footer-grid">
            <div class="footer-brand">
                <a class="logo" href="<?= url('index.php') ?>">Must<span>ala.</span></a>
                <p><?= e(__('hero_subtitle')) ?></p>
                <div class="social-icons">
                    <a href="<?= e(OWNER_FACEBOOK) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_facebook')) ?>"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_whatsapp')) ?>"><i class="fab fa-whatsapp"></i></a>
                    <a href="<?= e(OWNER_LINKEDIN) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_linkedin')) ?>"><i class="fab fa-linkedin-in"></i></a>
                    <a href="<?= e(OWNER_GITHUB) ?>" target="_blank" rel="noopener" aria-label="<?= e(__('aria_github')) ?>"><i class="fab fa-github"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4><?= e(__('nav_home')) ?></h4>
                <ul class="footer-links">
                    <li><a href="<?= url('about.php') ?>"><?= e(__('nav_about')) ?></a></li>
                    <li><a href="<?= url('services.php') ?>"><?= e(__('nav_services')) ?></a></li>
                    <li><a href="<?= url('projects.php') ?>"><?= e(__('nav_portfolio')) ?></a></li>
                    <li><a href="<?= url('blog.php') ?>"><?= e(__('nav_blog')) ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= e(__('nav_pricing')) ?></h4>
                <ul class="footer-links">
                    <li><a href="<?= url('gallery.php') ?>"><?= e(__('nav_gallery')) ?></a></li>
                    <li><a href="<?= url('testimonials.php') ?>"><?= e(__('nav_testimonials')) ?></a></li>
                    <li><a href="<?= url('faq.php') ?>"><?= e(__('nav_faq')) ?></a></li>
                    <li><a href="<?= url('contact.php') ?>"><?= e(__('nav_contact')) ?></a></li>
                </ul>
            </div>

            <div class="footer-col footer-contact">
                <h4><?= e(OWNER_NAME) ?></h4>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i> <?= e(OWNER_LOCATION) ?></li>
                    <li><a href="mailto:<?= e(OWNER_EMAIL) ?>"><i class="fas fa-envelope"></i> <?= e(OWNER_EMAIL) ?></a></li>
                    <li><a href="https://wa.me/<?= e(OWNER_WHATSAPP) ?>" target="_blank" rel="noopener"><i class="fab fa-whatsapp"></i> +<?= e(OWNER_WHATSAPP) ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="max-width">
                <?= e(__('footer_created')) ?> <a href="<?= url('about.php') ?>"><?= e(OWNER_NAME) ?></a>
                &copy; <?= date('Y') ?> <?= e(__('footer_rights')) ?>
            </div>
        </div>
    </footer>

    <div class="scroll-up-btn" id="scroll-top" aria-label="<?= e(__('aria_scroll_top')) ?>" role="button" tabindex="0">
        <i class="fa-solid fa-angle-up"></i>
    </div>

    <div class="lightbox" id="lightbox" hidden>
        <button type="button" class="lightbox-nav lightbox-nav--prev" id="lightbox-prev" aria-label="<?= e(__('aria_prev_image')) ?>" hidden><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
        <button type="button" class="lightbox-nav lightbox-nav--next" id="lightbox-next" aria-label="<?= e(__('aria_next_image')) ?>" hidden><i class="fas fa-chevron-right" aria-hidden="true"></i></button>
        <button type="button" class="lightbox-close" aria-label="<?= e(__('aria_close')) ?>">&times;</button>
        <img src="" alt="" id="lightbox-img">
        <video id="lightbox-video" controls playsinline></video>
        <div class="lightbox-dots" id="lightbox-dots" hidden></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.1.0/typed.umd.js"></script>
    <script>
      window.MUSTALA_TYPED = {
        hero: <?= json_encode(getTypedRoleStrings(), JSON_UNESCAPED_UNICODE) ?>,
        about: <?= json_encode(getTypedRoleStringsAlt(), JSON_UNESCAPED_UNICODE) ?>
      };
    </script>
    <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
