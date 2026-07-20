<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'categories';
$adminTitle = 'Categories';
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
    $db->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
    flash('success', 'Category deleted.');
    redirect('admin/categories.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $type = $_POST['type'] ?? 'blog';
    if (!in_array($type, ['blog', 'portfolio', 'gallery'], true)) $type = 'blog';
    $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
    $editId = (int)($_POST['id'] ?? 0);

    if ($editId) {
        $db->prepare('UPDATE categories SET name=?, slug=?, type=? WHERE id=?')->execute([$name, $slug, $type, $editId]);
        flash('success', 'Category updated.');
    } else {
        $db->prepare('INSERT INTO categories (name, slug, type) VALUES (?,?,?)')->execute([$name, $slug, $type]);
        flash('success', 'Category created.');
    }
    redirect('admin/categories.php');
}

$item = null;
if ($action === 'edit' && $id) {
    $stmt = $db->prepare('SELECT * FROM categories WHERE id=?');
    $stmt->execute([$id]);
    $item = $stmt->fetch();
}

$rows = $db->query('SELECT * FROM categories ORDER BY type, name')->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
    <h2><?= $item ? 'Edit Category' : 'Add Category' ?></h2>
    <form method="post" class="form-grid two">
        <input type="hidden" name="id" value="<?= (int)($item['id'] ?? 0) ?>">
        <div>
            <label>Name</label>
            <input name="name" required value="<?= e($item['name'] ?? '') ?>">
        </div>
        <div>
            <label>Slug</label>
            <input name="slug" value="<?= e($item['slug'] ?? '') ?>">
        </div>
        <div>
            <label>Type</label>
            <select name="type">
                <?php foreach (['blog','portfolio','gallery'] as $t): ?>
                <option value="<?= $t ?>" <?= (($item['type'] ?? '') === $t) ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="align-self:end">
            <button class="btn" type="submit">Save Category</button>
        </div>
    </form>
</div>

<div class="panel">
    <table>
        <thead><tr><th>Name</th><th>Slug</th><th>Type</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?= e($r['name']) ?></td>
                <td><?= e($r['slug']) ?></td>
                <td><?= e($r['type']) ?></td>
                <td class="actions">
                    <a class="btn btn-sm btn-secondary" href="<?= url('admin/categories.php?action=edit&id=' . $r['id']) ?>">Edit</a>
                    <a class="btn btn-sm btn-danger" href="<?= url('admin/categories.php?action=delete&id=' . $r['id']) ?>" onclick="return confirm('Delete?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
