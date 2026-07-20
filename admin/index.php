<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'dashboard';
$adminTitle = 'Dashboard';
$adminSubtitle = 'Overview of your Mustala portfolio content';

function countTable(string $table): int
{
    $db = getDB();
    if (!$db) return 0;
    try {
        return (int)$db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

$db = getDB();
$unread = 0;
$recentMessages = [];
$recentPosts = [];
$recentProjects = [];

if ($db) {
    try {
        $unread = (int)$db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn();
        $recentMessages = $db->query('SELECT id, name, email, subject, message, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5')->fetchAll();
        $recentPosts = $db->query('SELECT id, title, status, created_at FROM blog_posts ORDER BY created_at DESC LIMIT 5')->fetchAll();
        $recentProjects = $db->query('SELECT id, title, status, created_at FROM projects ORDER BY created_at DESC LIMIT 5')->fetchAll();
    } catch (Exception $e) {
        // tables may be missing
    }
}

$stats = [
    ['label' => 'Blog Posts', 'value' => countTable('blog_posts'), 'icon' => 'fa-newspaper', 'href' => 'admin/blog.php'],
    ['label' => 'Projects', 'value' => countTable('projects'), 'icon' => 'fa-briefcase', 'href' => 'admin/portfolio.php'],
    ['label' => 'Gallery Items', 'value' => countTable('gallery'), 'icon' => 'fa-images', 'href' => 'admin/gallery.php'],
    ['label' => 'Testimonials', 'value' => countTable('testimonials'), 'icon' => 'fa-star', 'href' => 'admin/testimonials.php'],
    ['label' => 'Categories', 'value' => countTable('categories'), 'icon' => 'fa-tags', 'href' => 'admin/categories.php'],
    ['label' => 'Unread Messages', 'value' => $unread, 'icon' => 'fa-envelope', 'href' => 'admin/messages.php'],
];

$adminActions = '<a class="btn" href="' . e(url('admin/blog.php?action=new')) . '"><i class="fas fa-plus"></i> New Post</a>'
    . '<a class="btn btn-secondary" href="' . e(url('index.php')) . '" target="_blank" rel="noopener"><i class="fas fa-external-link-alt"></i> View Site</a>';

require __DIR__ . '/includes/header.php';
?>

<div class="stats">
    <?php foreach (array_slice($stats, 0, 4) as $s): ?>
    <a class="stat" href="<?= url($s['href']) ?>">
        <div>
            <div class="label"><?= e($s['label']) ?></div>
            <div class="value"><?= (int)$s['value'] ?></div>
        </div>
        <div class="icon"><i class="fas <?= e($s['icon']) ?>"></i></div>
    </a>
    <?php endforeach; ?>
</div>

<div class="stats">
    <?php foreach (array_slice($stats, 4) as $s): ?>
    <a class="stat" href="<?= url($s['href']) ?>">
        <div>
            <div class="label"><?= e($s['label']) ?></div>
            <div class="value"><?= (int)$s['value'] ?></div>
        </div>
        <div class="icon"><i class="fas <?= e($s['icon']) ?>"></i></div>
    </a>
    <?php endforeach; ?>
    <div class="stat">
        <div>
            <div class="label">Signed in as</div>
            <div class="value" style="font-size:1.25rem;margin-top:.5rem"><?= e($_SESSION['admin_user'] ?? 'Admin') ?></div>
        </div>
        <div class="icon"><i class="fas fa-user-shield"></i></div>
    </div>
    <div class="stat">
        <div>
            <div class="label">Database</div>
            <div class="value" style="font-size:1.25rem;margin-top:.5rem"><?= $db ? 'Connected' : 'Offline' ?></div>
        </div>
        <div class="icon"><i class="fas fa-database"></i></div>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Quick Actions</h2>
    </div>
    <div class="quick-grid">
        <a class="quick-card" href="<?= url('admin/blog.php?action=new') ?>"><i class="fas fa-plus"></i> New Blog Post</a>
        <a class="quick-card" href="<?= url('admin/portfolio.php?action=new') ?>"><i class="fas fa-briefcase"></i> New Project</a>
        <a class="quick-card" href="<?= url('admin/gallery.php?action=new') ?>"><i class="fas fa-upload"></i> Upload Media</a>
        <a class="quick-card" href="<?= url('admin/testimonials.php?action=new') ?>"><i class="fas fa-star"></i> Add Testimonial</a>
        <a class="quick-card" href="<?= url('admin/categories.php') ?>"><i class="fas fa-tags"></i> Manage Categories</a>
        <a class="quick-card" href="<?= url('admin/messages.php') ?>"><i class="fas fa-envelope"></i> Inbox<?= $unread ? " ($unread new)" : '' ?></a>
        <a class="quick-card" href="<?= url('admin/settings.php') ?>"><i class="fas fa-gear"></i> Site Settings</a>
    </div>
</div>

<div class="dash-grid">
    <div class="panel">
        <div class="panel-head">
            <h2>Recent Messages</h2>
            <a class="btn btn-sm btn-secondary" href="<?= url('admin/messages.php') ?>">View all</a>
        </div>
        <?php if (!$db): ?>
            <p class="empty-note">Connect MySQL and import <code>database/schema.sql</code> to load data.</p>
        <?php elseif (!$recentMessages): ?>
            <p class="empty-note">No contact messages yet.</p>
        <?php else: ?>
        <div class="recent-list">
            <?php foreach ($recentMessages as $m): ?>
            <div class="recent-item">
                <a class="recent-link" href="<?= url('admin/messages.php?id=' . (int)$m['id']) ?>">
                    <strong><?= e($m['subject']) ?></strong>
                    <span><?= e($m['name']) ?> · <?= e(formatDate($m['created_at'], 'M j, Y H:i')) ?></span>
                </a>
                <span class="status-pill <?= $m['is_read'] ? 'read' : 'new' ?>"><?= $m['is_read'] ? 'Read' : 'New' ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Latest Blog Posts</h2>
            <a class="btn btn-sm btn-secondary" href="<?= url('admin/blog.php') ?>">Manage</a>
        </div>
        <?php if (!$recentPosts): ?>
            <p class="empty-note">No blog posts yet.</p>
        <?php else: ?>
        <div class="recent-list">
            <?php foreach ($recentPosts as $p): ?>
            <div class="recent-item">
                <div>
                    <strong><?= e($p['title']) ?></strong>
                    <span><?= e(formatDate($p['created_at'])) ?></span>
                </div>
                <span class="status-pill <?= $p['status'] === 'published' ? 'published' : 'draft' ?>"><?= e(ucfirst($p['status'])) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="panel">
    <div class="panel-head">
        <h2>Latest Projects</h2>
        <a class="btn btn-sm btn-secondary" href="<?= url('admin/portfolio.php') ?>">Manage</a>
    </div>
    <?php if (!$recentProjects): ?>
        <p class="empty-note">No projects yet.</p>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentProjects as $p): ?>
                <tr>
                    <td><strong><?= e($p['title']) ?></strong></td>
                    <td><span class="status-pill <?= $p['status'] === 'published' ? 'published' : 'draft' ?>"><?= e(ucfirst($p['status'])) ?></span></td>
                    <td><?= e(formatDate($p['created_at'])) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm btn-secondary" href="<?= url('admin/portfolio.php?action=edit&id=' . (int)$p['id']) ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
