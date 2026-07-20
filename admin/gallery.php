<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'gallery';
$adminTitle = 'Gallery';
$db = getDB();
$action = $_GET['action'] ?? 'list';
$id = (int)($_GET['id'] ?? 0);

if (!$db) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="panel"><p>Connect MySQL first.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($action === 'delete' && $id) {
    $stmt = $db->prepare('SELECT file_path FROM gallery WHERE id=?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['file_path'])) {
        $path = UPLOAD_PATH . '/' . $row['file_path'];
        if (is_file($path)) @unlink($path);
    }
    $db->prepare('DELETE FROM gallery WHERE id=?')->execute([$id]);
    flash('success', 'Item deleted.');
    redirect('admin/gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titleBase = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $media_type = ($_POST['media_type'] ?? 'image') === 'video' ? 'video' : 'image';
    $allowed = $media_type === 'video' ? ['mp4', 'webm', 'ogg'] : ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    $result = uploadFiles($_FILES['media'] ?? [], 'gallery', $allowed);
    $saved = 0;

    foreach ($result['uploaded'] as $i => $filePath) {
        $title = $titleBase !== ''
            ? ($i === 0 ? $titleBase : $titleBase . ' ' . ($i + 1))
            : pathinfo($filePath, PATHINFO_FILENAME);
        $db->prepare('INSERT INTO gallery (title, description, file_path, media_type, category_id) VALUES (?,?,?,?,?)')
            ->execute([$title, $description, $filePath, $media_type, $category_id]);
        $saved++;
    }

    if ($saved > 0) {
        $msg = $saved === 1 ? '1 file uploaded.' : $saved . ' files uploaded.';
        if ($result['failed'] > 0) {
            $msg .= ' ' . $result['failed'] . ' file(s) failed (type/size).';
            flash('error', $msg);
        } else {
            flash('success', $msg);
        }
    } else {
        flash('error', 'Upload failed. Use valid images (jpg/png/webp/gif ≤ 8MB) or videos (mp4/webm ≤ 50MB).');
    }
    redirect('admin/gallery.php');
}

$categories = getCategories('gallery');

if ($action === 'new') {
    $adminActions = '<a class="btn btn-secondary" href="' . url('admin/gallery.php') . '">Back</a>';
    $adminSubtitle = 'Drag & drop one or many files';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
        <form method="post" enctype="multipart/form-data" class="form-grid" id="gallery-upload-form">
            <div>
                <label>Title (optional for multi-upload — files get numbered)</label>
                <input name="title" placeholder="e.g. Project screenshots">
            </div>
            <div>
                <label>Description</label>
                <textarea name="description" placeholder="Optional caption for these files"></textarea>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Type</label>
                    <select name="media_type" id="media_type">
                        <option value="image">Image</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                <div>
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">— None —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label>Media files</label>
                <div class="upload-zone" data-upload-zone data-accept="image/*" data-multiple="true" id="gallery-zone">
                    <input type="file" name="media[]" accept="image/*" multiple required data-upload-input>
                    <div class="upload-zone-inner">
                        <i class="fas fa-cloud-arrow-up"></i>
                        <strong>Drag & drop images here</strong>
                        <span>or click to browse · multiple files supported · jpg/png/webp/gif · max 8MB each</span>
                        <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Choose files</button>
                    </div>
                    <div class="upload-preview" data-upload-preview></div>
                </div>
            </div>
            <button class="btn" type="submit"><i class="fas fa-upload"></i> Upload</button>
        </form>
    </div>
    <script>
    (function () {
      var type = document.getElementById('media_type');
      var input = document.querySelector('#gallery-zone [data-upload-input]');
      var hint = document.querySelector('#gallery-zone .upload-zone-inner span');
      if (!type || !input) return;
      type.addEventListener('change', function () {
        if (type.value === 'video') {
          input.accept = 'video/mp4,video/webm,video/ogg';
          if (hint) hint.textContent = 'or click to browse · mp4/webm · max 50MB each';
        } else {
          input.accept = 'image/*';
          if (hint) hint.textContent = 'or click to browse · multiple files supported · jpg/png/webp/gif · max 8MB each';
        }
      });
    })();
    </script>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$items = $db->query('SELECT g.*, c.name AS category_name FROM gallery g LEFT JOIN categories c ON c.id=g.category_id ORDER BY g.created_at DESC')->fetchAll();
$adminActions = '<a class="btn" href="' . url('admin/gallery.php?action=new') . '"><i class="fas fa-upload"></i> Upload</a>';
$adminSubtitle = count($items) . ' items';
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <div class="table-wrap">
    <table>
        <thead><tr><th>Preview</th><th>Title</th><th>Type</th><th>Category</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td>
                    <?php if ($item['media_type'] === 'image'): ?>
                        <img class="thumb thumb-lg" src="<?= e(mediaUrl($item['file_path'])) ?>" alt="">
                    <?php else: ?>
                        <div class="thumb thumb-lg" style="display:grid;place-items:center;background:#111;color:#fff"><i class="fas fa-video"></i></div>
                    <?php endif; ?>
                </td>
                <td><?= e($item['title']) ?></td>
                <td><span class="status-pill read"><?= e($item['media_type']) ?></span></td>
                <td><?= e($item['category_name'] ?? '—') ?></td>
                <td>
                    <a class="btn btn-sm btn-danger" href="<?= url('admin/gallery.php?action=delete&id=' . $item['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$items): ?>
            <tr><td colspan="5" class="empty-note">No gallery items yet. <a href="<?= url('admin/gallery.php?action=new') ?>" style="color:var(--a-crimson);font-weight:600">Upload media</a></td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
