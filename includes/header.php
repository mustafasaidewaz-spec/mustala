<?php
require_once __DIR__ . '/init.php';

$pageTitle = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? __('meta_home_desc');
$pageKeywords = $pageKeywords ?? 'web developer, freelance, wordpress, mozambique, mustala, mustafa saide';
$pageImage = $pageImage ?? asset('img/og-default.svg');
$canonical = $canonical ?? (isset($_SERVER['HTTP_HOST'])
    ? ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . ($_SERVER['REQUEST_URI'] ?? '/')
    : url());
$lang = getLang();
$flash = getFlash();
$isHome = currentPage() === 'home';
$bodyClass = trim(($bodyClass ?? '') . ' ' . ($isHome ? 'home-page' : 'inner-page'));
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta name="keywords" content="<?= e($pageKeywords) ?>">
    <meta name="author" content="<?= e(OWNER_NAME) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= e($canonical) ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:image" content="<?= e($pageImage) ?>">
    <meta property="og:url" content="<?= e($canonical) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <?= renderThemeStyles() ?>
</head>
<body class="<?= e($bodyClass) ?>">
    <a class="skip-link" href="#main"><?= e(__('skip_to_content')) ?></a>

    <nav class="navbar" id="navbar">
        <div class="max-width navbar-inner">
            <div class="logo">
                <a href="<?= url('index.php') ?>">Must<span>ala</span></a>
            </div>

            <ul class="menu" id="main-nav">
                <li><a class="<?= isActive('home') ?>" href="<?= url('index.php') ?>"><?= e(__('nav_home')) ?></a></li>
                <li><a class="<?= isActive('about') ?>" href="<?= url('about.php') ?>"><?= e(__('nav_about')) ?></a></li>
                <li><a href="<?= url('about.php#skills') ?>"><?= e(__('skills')) ?></a></li>
                <li><a class="<?= isActive('services') ?>" href="<?= url('services.php') ?>"><?= e(__('nav_services')) ?></a></li>
                <li><a class="<?= in_array(currentPage(), ['projects', 'portfolio', 'project'], true) ? 'active' : '' ?>" href="<?= url('projects.php') ?>"><?= e(__('nav_portfolio')) ?></a></li>
                <li><a class="<?= isActive('blog') ?>" href="<?= url('blog.php') ?>"><?= e(__('nav_blog')) ?></a></li>
                <li><a class="<?= isActive('contact') ?>" href="<?= url('contact.php') ?>"><?= e(__('nav_contact')) ?></a></li>
                <li class="menu-mobile-only menu-hire-wrap">
                    <a class="menu-hire btn" href="<?= url('contact.php') ?>"><?= e(__('cta_hire')) ?></a>
                </li>
            </ul>

            <div class="header-actions">
                <?php require __DIR__ . '/lang-switcher.php'; ?>
                <a class="nav-cta" href="<?= url('contact.php') ?>"><?= e(__('cta_hire')) ?></a>
                <button type="button" class="menu-btn" id="menu-toggle" aria-label="<?= e(__('aria_menu')) ?>" aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>" role="status">
        <div class="max-width"><?= e($flash['message']) ?></div>
    </div>
    <?php endif; ?>

    <main id="main">
