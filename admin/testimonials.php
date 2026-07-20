<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'testimonials';
$adminTitle = 'Testimonials';
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
    $db->prepare('DELETE FROM testimonials WHERE id=?')->execute([$id]);
    flash('success', 'Testimonial deleted.');
    redirect('admin/testimonials.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = trim($_POST['client_name'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $status = ($_POST['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
    $is_featured = !empty($_POST['is_featured']) ? 1 : 0;
    $editId = (int)($_POST['id'] ?? 0);

    $clientImage = null;
    $companyLogo = null;
    if (!empty($_FILES['client_image']['name'])) {
        $clientImage = uploadFile($_FILES['client_image'], 'testimonials');
    }
    if (!empty($_FILES['company_logo']['name'])) {
        $companyLogo = uploadFile($_FILES['company_logo'], 'testimonials');
    }

    if ($editId) {
        $fields = 'client_name=?, company=?, role=?, content=?, rating=?, is_featured=?, status=?';
        $params = [$client_name, $company, $role, $content, $rating, $is_featured, $status];
        if ($clientImage) { $fields .= ', client_image=?'; $params[] = $clientImage; }
        if ($companyLogo) { $fields .= ', company_logo=?'; $params[] = $companyLogo; }
        $params[] = $editId;
        $db->prepare("UPDATE testimonials SET $fields WHERE id=?")->execute($params);
        flash('success', 'Updated.');
    } else {
        $db->prepare('INSERT INTO testimonials (client_name, company, role, content, rating, client_image, company_logo, is_featured, status) VALUES (?,?,?,?,?,?,?,?,?)')
            ->execute([$client_name, $company, $role, $content, $rating, $clientImage, $companyLogo, $is_featured, $status]);
        flash('success', 'Created.');
    }
    redirect('admin/testimonials.php');
}

$item = null;
if ($action === 'edit' || $action === 'new') {
    if ($action === 'edit' && $id) {
        $stmt = $db->prepare('SELECT * FROM testimonials WHERE id=?');
        $stmt->execute([$id]);
        $item = $stmt->fetch();
    }
    $adminActions = '<a class="btn btn-secondary" href="' . url('admin/testimonials.php') . '">Back</a>';
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
            <div class="form-grid two">
                <div><label>Client Name</label><input name="client_name" required value="<?= e($item['client_name'] ?? '') ?>"></div>
                <div><label>Company</label><input name="company" value="<?= e($item['company'] ?? '') ?>"></div>
            </div>
            <div class="form-grid two">
                <div><label>Role</label><input name="role" value="<?= e($item['role'] ?? '') ?>"></div>
                <div><label>Rating (1-5)</label><input type="number" min="1" max="5" name="rating" value="<?= (int)($item['rating'] ?? 5) ?>"></div>
            </div>
            <div><label>Review</label><textarea name="content" required><?= e($item['content'] ?? '') ?></textarea></div>
            <div class="form-grid two">
                <div>
                    <label>Client Image</label>
                    <div class="upload-zone" data-upload-zone data-accept="image/*" data-multiple="false">
                        <input type="file" name="client_image" accept="image/*" data-upload-input>
                        <div class="upload-zone-inner">
                            <i class="fas fa-user"></i>
                            <strong>Client photo</strong>
                            <span>jpg/png/webp · max 8MB</span>
                            <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Browse</button>
                        </div>
                        <div class="upload-preview" data-upload-preview></div>
                    </div>
                </div>
                <div>
                    <label>Company Logo</label>
                    <div class="upload-zone" data-upload-zone data-accept="image/*" data-multiple="false">
                        <input type="file" name="company_logo" accept="image/*" data-upload-input>
                        <div class="upload-zone-inner">
                            <i class="fas fa-building"></i>
                            <strong>Company logo</strong>
                            <span>jpg/png/webp · max 8MB</span>
                            <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Browse</button>
                        </div>
                        <div class="upload-preview" data-upload-preview></div>
                    </div>
                </div>
            </div>
            <div class="form-grid two">
                <div>
                    <label>Status</label>
                    <select name="status">
                        <option value="published">Published</option>
                        <option value="draft" <?= (($item['status'] ?? '') === 'draft') ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
                <div><label><input type="checkbox" name="is_featured" value="1" <?= !empty($item['is_featured']) ? 'checked' : '' ?>> Featured</label></div>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

$rows = $db->query('SELECT * FROM testimonials ORDER BY created_at DESC')->fetchAll();
$adminActions = '<a class="btn" href="' . url('admin/testimonials.php?action=new') . '"><i class="fas fa-plus"></i> Add</a>';
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <table>
        <thead><tr><th>Client</th><th>Company</th><th>Rating</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['client_name']) ?></td>
                <td><?= e($r['company'] ?? '—') ?></td>
                <td><?= (int)$r['rating'] ?>/5</td>
                <td><?= e($r['status']) ?></td>
                <td class="actions">
                    <a class="btn btn-sm btn-secondary" href="<?= url('admin/testimonials.php?action=edit&id=' . $r['id']) ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="<?= url('admin/testimonials.php?action=delete&id=' . $r['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
