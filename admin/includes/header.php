<?php
require_once dirname(__DIR__, 2) . '/includes/functions.php';
requireAdmin();

$adminPage = $adminPage ?? 'dashboard';
$adminTitle = $adminTitle ?? 'Dashboard';
$adminSubtitle = $adminSubtitle ?? '';

$unreadMessages = 0;
$_db = getDB();
if ($_db) {
    try {
        $unreadMessages = (int)$_db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();
    } catch (Exception $e) {
        $unreadMessages = 0;
    }
}
$adminName = $_SESSION['admin_user'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e($adminTitle) ?> — Mustala Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <?= renderAdminThemeStyles() ?>
</head>
<body class="admin-body">
<div class="overlay-admin" id="admin-overlay"></div>
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="brand">
            Must<span>ala</span>
            <span class="brand-sub">Admin Panel</span>
        </div>
        <nav class="admin-nav">
            <div class="nav-label">Main</div>
            <a class="<?= $adminPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('admin/index.php') ?>"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a class="<?= $adminPage === 'messages' ? 'active' : '' ?>" href="<?= url('admin/messages.php') ?>">
                <i class="fas fa-envelope"></i> Messages
                <?php if ($unreadMessages > 0): ?><span class="badge"><?= $unreadMessages ?></span><?php endif; ?>
            </a>

            <div class="nav-label">Content</div>
            <a class="<?= $adminPage === 'blog' ? 'active' : '' ?>" href="<?= url('admin/blog.php') ?>"><i class="fas fa-newspaper"></i> Blog</a>
            <a class="<?= $adminPage === 'portfolio' ? 'active' : '' ?>" href="<?= url('admin/portfolio.php') ?>"><i class="fas fa-briefcase"></i> Projects</a>
            <a class="<?= $adminPage === 'gallery' ? 'active' : '' ?>" href="<?= url('admin/gallery.php') ?>"><i class="fas fa-images"></i> Gallery</a>
            <a class="<?= $adminPage === 'testimonials' ? 'active' : '' ?>" href="<?= url('admin/testimonials.php') ?>"><i class="fas fa-star"></i> Testimonials</a>
            <a class="<?= $adminPage === 'categories' ? 'active' : '' ?>" href="<?= url('admin/categories.php') ?>"><i class="fas fa-tags"></i> Categories</a>

            <div class="nav-label">System</div>
            <a class="<?= $adminPage === 'settings' ? 'active' : '' ?>" href="<?= url('admin/settings.php') ?>"><i class="fas fa-gear"></i> Settings</a>
            <a href="<?= url('index.php') ?>" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> View Site</a>
            <a href="<?= url('admin/logout.php') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
        <div class="sidebar-foot">
            <div class="sidebar-user">
                <div class="avatar"><?= e(mb_strtoupper(mb_substr($adminName, 0, 1))) ?></div>
                <div>
                    <strong><?= e($adminName) ?></strong>
                    <span>Administrator</span>
                </div>
            </div>
        </div>
    </aside>
    <main class="admin-main">
        <?php $flash = getFlash(); if ($flash): ?>
            <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
        <?php endif; ?>
        <div class="admin-top">
            <div style="display:flex;align-items:center;gap:.75rem">
                <button type="button" class="menu-toggle-admin" id="admin-menu-toggle" aria-label="Open menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <h1><?= e($adminTitle) ?></h1>
                    <?php if ($adminSubtitle !== ''): ?>
                        <div class="subtitle"><?= e($adminSubtitle) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="actions">
                <?php if (!empty($adminActions)) echo $adminActions; ?>
            </div>
        </div>
