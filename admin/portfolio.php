<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'portfolio';
$adminTitle = 'Projects';
$db = getDB();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if (!$db) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="panel"><p>Connect MySQL and import <code>database/schema.sql</code>.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($action === 'delete' && $id) {
    $db->prepare('DELETE FROM projects WHERE id=?')->execute([$id]);
    flash('success', 'Project deleted.');
    redirect('admin/portfolio.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($title);
    $description = trim($_POST['description'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $tech_stack = trim($_POST['tech_stack'] ?? '');
    $live_demo = trim($_POST['live_demo'] ?? '') ?: null;
    $github_url = trim($_POST['github_url'] ?? '') ?: null;
    $video_url = trim($_POST['video_url'] ?? '') ?: null;
    $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $is_featured = !empty($_POST['is_featured']) ? 1 : 0;
    $editId = (int)($_POST['id'] ?? 0);

    $imagePath = null;
    if (!empty($_FILES['featured_image']['name'])) {
        $imagePath = uploadFile($_FILES['featured_image'], 'portfolio', ['jpg','jpeg','png','gif','webp']);
    }

    if ($editId) {
        if ($imagePath) {
            $db->prepare('UPDATE projects SET title=?, slug=?, description=?, content=?, category_id=?, featured_image=?, video_url=?, tech_stack=?, live_demo=?, github_url=?, is_featured=?, status=? WHERE id=?')
                ->execute([$title, $slug, $description, $content, $category_id, $imagePath, $video_url, $tech_stack, $live_demo, $github_url, $is_featured, $status, $editId]);
        } else {
            $db->prepare('UPDATE projects SET title=?, slug=?, description=?, content=?, category_id=?, video_url=?, tech_stack=?, live_demo=?, github_url=?, is_featured=?, status=? WHERE id=?')
                ->execute([$title, $slug, $description, $content, $category_id, $video_url, $tech_stack, $live_demo, $github_url, $is_featured, $status, $editId]);
        }
        $projectId = $editId;
        flash('success', 'Project updated.');
    } else {
        $db->prepare('INSERT INTO projects (title, slug, description, content, category_id, featured_image, video_url, tech_stack, live_demo, github_url, is_featured, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
            ->execute([$title, $slug, $description, $content, $category_id, $imagePath, $video_url, $tech_stack, $live_demo, $github_url, $is_featured, $status]);
        $projectId = (int)$db->lastInsertId();
        flash('success', 'Project created.');
    }

    if (!empty($_FILES['screenshots']['name'][0])) {
        foreach ($_FILES['screenshots']['name'] as $i => $name) {
            if (!$name) continue;
            $file = [
                'name' => $_FILES['screenshots']['name'][$i],
                'type' => $_FILES['screenshots']['type'][$i],
                'tmp_name' => $_FILES['screenshots']['tmp_name'][$i],
                'error' => $_FILES['screenshots']['error'][$i],
                'size' => $_FILES['screenshots']['size'][$i],
            ];
            $path = uploadFile($file, 'portfolio', ['jpg','jpeg','png','gif','webp']);
            if ($path && $projectId) {
                $db->prepare('INSERT INTO project_images (project_id, image_path, sort_order) VALUES (?,?,?)')
                    ->execute([$projectId, $path, $i]);
            }
        }
    }

    redirect('admin/portfolio.php');
}

$categories = getCategories('portfolio');
$project = null;
if ($action === 'edit' || $action === 'new') {
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM projects WHERE id=?');
        $stmt->execute([$id]);
        $project = $stmt->fetch();
    }
    $adminActions = '<a class="btn btn-secondary" href="' . url('admin/portfolio.php') . '">Back</a>';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id" value="<?= (int)($project['id'] ?? 0) ?>">
            <div class="form-grid two">
                <div>
                    <label>Title</label>
                    <input name="title" required value="<?= e($project['title'] ?? '') ?>">
                </div>
                <div>
                    <label>Slug</label>
                    <input name="slug" value="<?= e($project['slug'] ?? '') ?>">
                </div>
            </div>
            <div>
                <label>Short Description</label>
                <textarea name="description"><?= e($project['description'] ?? '') ?></textarea>
            </div>
            <div>
                <label>Details (HTML)</label>
                <textarea name="content" style="min-height:200px"><?= e($project['content'] ?? '') ?></textarea>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (($project['category_id'] ?? '') == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label>Tech Stack (comma separated)</label>
                    <input name="tech_stack" value="<?= e($project['tech_stack'] ?? '') ?>">
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Live Demo URL</label>
                    <input name="live_demo" value="<?= e($project['live_demo'] ?? '') ?>">
                </div>
                <div>
                    <label>GitHub URL</label>
                    <input name="github_url" value="<?= e($project['github_url'] ?? '') ?>">
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Video URL / path</label>
                    <input name="video_url" value="<?= e($project['video_url'] ?? '') ?>">
                </div>
            </div>
            <div>
                <label>Featured Image</label>
                <?php if (!empty($project['featured_image'])): ?>
                <div class="current-thumb">
                    <img src="<?= e(mediaUrl($project['featured_image'])) ?>" alt="">
                    <span style="color:var(--a-muted);font-size:.85rem">Current image — upload to replace</span>
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
                <label>Additional Screenshots</label>
                <div class="upload-zone" data-upload-zone data-accept="image/*" data-multiple="true">
                    <input type="file" name="screenshots[]" accept="image/*" multiple data-upload-input>
                    <div class="upload-zone-inner">
                        <i class="fas fa-images"></i>
                        <strong>Drag & drop screenshots</strong>
                        <span>multiple images · max 8MB each</span>
                        <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Choose files</button>
                    </div>
                    <div class="upload-preview" data-upload-preview></div>
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="published" <?= (($project['status'] ?? '') === 'published') ? 'selected' : '' ?>>Published</option>
                        <option value="draft" <?= (($project['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div>
                    <label><input type="checkbox" name="is_featured" value="1" <?= !empty($project['is_featured']) ? 'checked' : '' ?>> Featured</label>
                </div>
            </div>
            <button class="btn" type="submit">Save Project</button>
        </form>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$items = $db->query('SELECT p.*, c.name AS category_name FROM projects p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC')->fetchAll();
$adminActions = '<a class="btn" href="' . url('admin/portfolio.php?action=new') . '"><i class="fas fa-plus"></i> New Project</a>';
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <table>
        <thead><tr><th>Title</th><th>Category</th><th>Status</th><th>Featured</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($items as $p): ?>
            <tr>
                <td><?= e($p['title']) ?></td>
                <td><?= e($p['category_name'] ?? '—') ?></td>
                <td><?= e($p['status']) ?></td>
                <td><?= $p['is_featured'] ? 'Yes' : 'No' ?></td>
                <td class="actions">
                    <a class="btn btn-sm btn-secondary" href="<?= url('admin/portfolio.php?action=edit&id=' . $p['id']) ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="<?= url('admin/portfolio.php?action=delete&id=' . $p['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
