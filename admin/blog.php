<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'blog';
$adminTitle = 'Blog Posts';
$db = getDB();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if (!$db) {
    $adminActions = '';
    require __DIR__ . '/includes/header.php';
    echo '<div class="panel"><p>Connect MySQL and import <code>database/schema.sql</code> to manage blog posts.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($action === 'delete' && $id) {
    $db->prepare('DELETE FROM blog_posts WHERE id=?')->execute([$id]);
    flash('success', 'Post deleted.');
    redirect('admin/blog.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $tags = trim($_POST['tags'] ?? '');
    $video_url = trim($_POST['video_url'] ?? '') ?: null;
    $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $is_featured = !empty($_POST['is_featured']) ? 1 : 0;
    $editId = (int)($_POST['id'] ?? 0);

    $imagePath = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $imagePath = uploadFile($_FILES['featured_image'], 'blog', ['jpg','jpeg','png','gif','webp']);
    }

    if ($editId) {
        if ($imagePath) {
            $db->prepare('UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, category_id=?, featured_image=?, video_url=?, tags=?, is_featured=?, status=? WHERE id=?')
                ->execute([$title, $slug, $excerpt, $content, $category_id, $imagePath, $video_url, $tags, $is_featured, $status, $editId]);
        } else {
            $db->prepare('UPDATE blog_posts SET title=?, slug=?, excerpt=?, content=?, category_id=?, video_url=?, tags=?, is_featured=?, status=? WHERE id=?')
                ->execute([$title, $slug, $excerpt, $content, $category_id, $video_url, $tags, $is_featured, $status, $editId]);
        }
        flash('success', 'Post updated.');
    } else {
        $db->prepare('INSERT INTO blog_posts (title, slug, excerpt, content, category_id, featured_image, video_url, tags, is_featured, status) VALUES (?,?,?,?,?,?,?,?,?,?)')
            ->execute([$title, $slug, $excerpt, $content, $category_id, $imagePath, $video_url, $tags, $is_featured, $status]);
        flash('success', 'Post created.');
    }
    redirect('admin/blog.php');
}

$categories = getCategories('blog');
$post = null;
if (($action === 'edit' || $action === 'new') ) {
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM blog_posts WHERE id=?');
        $stmt->execute([$id]);
        $post = $stmt->fetch();
    }
    $adminActions = '<a class="btn btn-secondary" href="' . url('admin/blog.php') . '">Back</a>';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id" value="<?= (int)($post['id'] ?? 0) ?>">
            <div class="form-grid two">
                <div>
                    <label>Title</label>
                    <input name="title" required value="<?= e($post['title'] ?? '') ?>">
                </div>
                <div>
                    <label>Slug</label>
                    <input name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="auto-generated">
                </div>
            </div>
            <div>
                <label>Excerpt</label>
                <textarea name="excerpt"><?= e($post['excerpt'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Content (HTML allowed)</label>
                <textarea name="content" style="min-height:220px"><?= e($post['content'] ?? '') ?></textarea>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (($post['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Tags (comma separated)</label>
                    <input name="tags" value="<?= e($post['tags'] ?? '') ?>">
                </div>
            </div>
            <div>
                <label>Featured Image</label>
                <?php if (!empty($post['featured_image'])): ?>
                <div class="current-thumb">
                    <img src="<?= e(mediaUrl($post['featured_image'])) ?>" alt="">
                    <span style="color:var(--a-muted);font-size:.85rem">Current image — upload a new file to replace it</span>
                </div>
                <?php endif; ?>
                <div class="upload-zone" data-upload-zone data-accept="image/*" data-multiple="false">
                    <input type="file" name="featured_image" accept="image/*" data-upload-input>
                    <div class="upload-zone-inner">
                        <i class="fas fa-image"></i>
                        <strong>Drag & drop featured image</strong>
                        <span>jpg/png/webp/gif · max 8MB</span>
                        <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Browse</button>
                    </div>
                    <div class="upload-preview" data-upload-preview></div>
                </div>
            </div>
            <div>
                <label>Video URL</label>
                <input name="video_url" value="<?= e($post['video_url'] ?? '') ?>">
            </div>
            <div class="form-grid two">
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="published" <?= (($post['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= (($post['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div>
                    <label><input type="checkbox" name="is_featured" value="1" <?= !empty($post['is_featured']) ? 'checked' : '' ?>> Featured</label>
                </div>
            </div>
            <button class="btn" type="submit">Save Post</button>
        </form>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$posts = $db->query('SELECT b.*, c.name AS category_name FROM blog_posts b LEFT JOIN categories c ON c.id=b.category_id ORDER BY b.created_at DESC')->fetchAll();
$adminActions = '<a class="btn" href="' . url('admin/blog.php?action=new') . '"><i class="fas fa-plus"></i> New Post</a>';
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <table>
        <thead>
            <tr><th>Title</th><th>Category</th><th>Status</th><th>Views</th><th>Actions</th></tr>
        </thead>
        <tbody>
            <?php foreach ($posts as $p): ?>
            <tr>
                <td><?= e($p['title']) ?></td>
                <td><?= e($p['category_name'] ?? '—') ?></td>
                <td><?= e($p['status']) ?></td>
                <td><?= (int)$p['views'] ?></td>
                <td class="actions">
                    <a class="btn btn-sm btn-secondary" href="<?= url('admin/blog.php?action=edit&id=' . $p['id']) ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="<?= url('admin/blog.php?action=delete&id=' . $p['id']) ?>" onclick="return confirm('Delete this post?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
