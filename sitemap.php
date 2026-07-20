<?php
require_once __DIR__ . '/includes/init.php';

header('Content-Type: application/xml; charset=utf-8');

$host = ($_SERVER['HTTPS'] ?? '') !== 'off' && !empty($_SERVER['HTTPS']) ? 'https' : 'http';
$host .= '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$base = $host . BASE_URL;

$pages = [
    'index.php', 'about.php', 'services.php', 'projects.php', 'blog.php',
    'gallery.php', 'testimonials.php', 'pricing.php', 'faq.php', 'contact.php',
];

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p): ?>
  <url><loc><?= htmlspecialchars($base . '/' . $p) ?></loc><changefreq>weekly</changefreq></url>
<?php endforeach; ?>
<?php
$db = getDB();
if ($db) {
    try {
        foreach ($db->query("SELECT slug, updated_at FROM projects WHERE status='published'") as $row) {
            echo '  <url><loc>' . htmlspecialchars($base . '/project.php?slug=' . urlencode($row['slug'])) . '</loc></url>' . "\n";
        }
        foreach ($db->query("SELECT slug, updated_at FROM blog_posts WHERE status='published'") as $row) {
            echo '  <url><loc>' . htmlspecialchars($base . '/post.php?slug=' . urlencode($row['slug'])) . '</loc></url>' . "\n";
        }
    } catch (Exception $e) {}
}
?>
</urlset>
