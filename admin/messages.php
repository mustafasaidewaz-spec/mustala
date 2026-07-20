<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'messages';
$adminTitle = 'Contact Messages';
$db = getDB();

if (!$db) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="panel"><p>Connect MySQL first.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action === 'read' && $id) {
        $db->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([$id]);
        flash('success', 'Message marked as read.');
    } elseif ($action === 'unread' && $id) {
        $db->prepare('UPDATE contact_messages SET is_read=0 WHERE id=?')->execute([$id]);
        flash('success', 'Message marked as unread.');
    } elseif ($action === 'delete' && $id) {
        $db->prepare('DELETE FROM contact_messages WHERE id=?')->execute([$id]);
        flash('success', 'Message deleted.');
        redirect('admin/messages.php');
    } elseif ($action === 'mark_all_read') {
        $db->exec('UPDATE contact_messages SET is_read=1 WHERE is_read=0');
        flash('success', 'All messages marked as read.');
    }

    $redirect = 'admin/messages.php';
    $qs = [];
    if (!empty($_POST['filter']) && $_POST['filter'] !== 'all') {
        $qs['filter'] = $_POST['filter'];
    }
    if (!empty($_POST['q'])) {
        $qs['q'] = $_POST['q'];
    }
    if ($action !== 'delete' && $id) {
        $qs['id'] = $id;
    }
    if ($qs) {
        $redirect .= '?' . http_build_query($qs);
    }
    redirect($redirect);
}

$filter = $_GET['filter'] ?? 'all';
if (!in_array($filter, ['all', 'unread', 'read'], true)) {
    $filter = 'all';
}
$q = trim($_GET['q'] ?? '');
$viewId = (int)($_GET['id'] ?? 0);

$stats = [
    'total' => (int)$db->query('SELECT COUNT(*) FROM contact_messages')->fetchColumn(),
    'unread' => (int)$db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=0')->fetchColumn(),
    'read' => (int)$db->query('SELECT COUNT(*) FROM contact_messages WHERE is_read=1')->fetchColumn(),
    'week' => (int)$db->query('SELECT COUNT(*) FROM contact_messages WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)')->fetchColumn(),
];

$sql = 'SELECT * FROM contact_messages WHERE 1=1';
$params = [];
if ($filter === 'unread') {
    $sql .= ' AND is_read=0';
} elseif ($filter === 'read') {
    $sql .= ' AND is_read=1';
}
if ($q !== '') {
    $sql .= ' AND (name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)';
    $like = '%' . $q . '%';
    $params = [$like, $like, $like, $like];
}
$sql .= ' ORDER BY created_at DESC';

$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$active = null;
if ($viewId) {
    foreach ($rows as $row) {
        if ((int)$row['id'] === $viewId) {
            $active = $row;
            break;
        }
    }
    if (!$active) {
        $detailStmt = $db->prepare('SELECT * FROM contact_messages WHERE id=? LIMIT 1');
        $detailStmt->execute([$viewId]);
        $active = $detailStmt->fetch() ?: null;
    }
    if ($active && !(int)$active['is_read']) {
        $db->prepare('UPDATE contact_messages SET is_read=1 WHERE id=?')->execute([(int)$active['id']]);
        $active['is_read'] = 1;
        foreach ($rows as &$row) {
            if ((int)$row['id'] === (int)$active['id']) {
                $row['is_read'] = 1;
            }
        }
        unset($row);
        if ($stats['unread'] > 0) {
            $stats['unread']--;
            $stats['read']++;
        }
    }
}

$listQuery = static function (array $extra = []) use ($filter, $q): string {
    $params = $extra;

    if (!array_key_exists('filter', $params)) {
        if ($filter !== 'all') {
            $params['filter'] = $filter;
        }
    } elseif ($params['filter'] === 'all' || $params['filter'] === null) {
        unset($params['filter']);
    }

    if (!array_key_exists('q', $params) && $q !== '') {
        $params['q'] = $q;
    } elseif (array_key_exists('q', $params) && $params['q'] === null) {
        unset($params['q']);
    }

    if (array_key_exists('id', $params) && $params['id'] === null) {
        unset($params['id']);
    }

    $params = array_filter($params, static fn($v) => $v !== '' && $v !== null);
    $query = http_build_query($params);
    return url('admin/messages.php' . ($query ? '?' . $query : ''));
};

$adminSubtitle = $stats['total'] . ' total · ' . $stats['unread'] . ' unread';
require __DIR__ . '/includes/header.php';
?>

<div class="msg-stats">
    <div class="stat">
        <div><div class="label">Total</div><div class="value"><?= $stats['total'] ?></div></div>
        <div class="icon"><i class="fas fa-inbox"></i></div>
    </div>
    <a class="stat" href="<?= e($listQuery(['filter' => 'unread'])) ?>">
        <div><div class="label">Unread</div><div class="value"><?= $stats['unread'] ?></div></div>
        <div class="icon"><i class="fas fa-envelope"></i></div>
    </a>
    <a class="stat" href="<?= e($listQuery(['filter' => 'read'])) ?>">
        <div><div class="label">Read</div><div class="value"><?= $stats['read'] ?></div></div>
        <div class="icon"><i class="fas fa-envelope-open"></i></div>
    </a>
    <div class="stat">
        <div><div class="label">This week</div><div class="value"><?= $stats['week'] ?></div></div>
        <div class="icon"><i class="fas fa-calendar-week"></i></div>
    </div>
</div>

<div class="panel inbox-panel">
    <div class="inbox-toolbar">
        <form class="inbox-search" method="get" action="<?= url('admin/messages.php') ?>">
            <?php if ($filter !== 'all'): ?>
            <input type="hidden" name="filter" value="<?= e($filter) ?>">
            <?php endif; ?>
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, email, subject…" autocomplete="off">
            <?php if ($q !== ''): ?>
            <a class="inbox-search-clear" href="<?= e($listQuery(['filter' => $filter === 'all' ? null : $filter, 'q' => null])) ?>" aria-label="Clear search">&times;</a>
            <?php endif; ?>
        </form>

        <div class="inbox-filters">
            <a class="inbox-filter <?= $filter === 'all' ? 'active' : '' ?>" href="<?= e($listQuery()) ?>">All</a>
            <a class="inbox-filter <?= $filter === 'unread' ? 'active' : '' ?>" href="<?= e($listQuery(['filter' => 'unread'])) ?>">Unread<?= $stats['unread'] ? ' (' . $stats['unread'] . ')' : '' ?></a>
            <a class="inbox-filter <?= $filter === 'read' ? 'active' : '' ?>" href="<?= e($listQuery(['filter' => 'read'])) ?>">Read</a>
        </div>

        <?php if ($stats['unread'] > 0): ?>
        <form method="post" class="inbox-mark-all">
            <input type="hidden" name="action" value="mark_all_read">
            <input type="hidden" name="filter" value="<?= e($filter) ?>">
            <input type="hidden" name="q" value="<?= e($q) ?>">
            <button type="submit" class="btn btn-sm btn-secondary">Mark all read</button>
        </form>
        <?php endif; ?>
    </div>

    <div class="inbox-layout<?= $active ? ' has-active' : '' ?>">
        <div class="inbox-list-wrap">
            <?php if (!$rows): ?>
            <div class="inbox-empty">
                <i class="fas fa-inbox"></i>
                <strong><?= $q !== '' ? 'No matches found' : 'No messages yet' ?></strong>
                <p><?= $q !== '' ? 'Try a different search or filter.' : 'Messages from your contact form will appear here.' ?></p>
            </div>
            <?php else: ?>
            <ul class="inbox-list">
                <?php foreach ($rows as $r): ?>
                <?php
                    $isActive = $active && (int)$active['id'] === (int)$r['id'];
                    $itemHref = $listQuery(['id' => $r['id'], 'filter' => $filter === 'all' ? null : $filter]);
                ?>
                <li class="inbox-item<?= !$r['is_read'] ? ' unread' : '' ?><?= $isActive ? ' active' : '' ?>">
                    <a href="<?= e($itemHref) ?>">
                        <div class="inbox-item-top">
                            <strong><?= e($r['name']) ?></strong>
                            <time><?= e(formatDate($r['created_at'], 'M j, H:i')) ?></time>
                        </div>
                        <div class="inbox-item-subject"><?= e($r['subject']) ?></div>
                        <div class="inbox-item-preview"><?= e(truncate($r['message'], 90)) ?></div>
                        <div class="inbox-item-meta">
                            <span class="status-pill <?= $r['is_read'] ? 'read' : 'new' ?>"><?= $r['is_read'] ? 'Read' : 'New' ?></span>
                            <span><?= e($r['email']) ?></span>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </div>

        <div class="inbox-detail<?= $active ? ' has-message' : '' ?>">
            <?php if (!$active): ?>
            <div class="inbox-detail-empty">
                <i class="fas fa-envelope-open-text"></i>
                <strong>Select a message</strong>
                <p>Choose a message from the list to read the full content and reply.</p>
            </div>
            <?php else: ?>
            <?php
                $replyBody = "Hi " . $active['name'] . ",\n\nThank you for your message.\n\n---\nOriginal message:\n" . $active['message'];
                $replyHref = 'mailto:' . rawurlencode($active['email'])
                    . '?subject=' . rawurlencode('Re: ' . $active['subject'])
                    . '&body=' . rawurlencode($replyBody);
            ?>
            <div class="inbox-detail-head">
                <a class="inbox-back" href="<?= e($listQuery(['id' => null, 'filter' => $filter === 'all' ? null : $filter])) ?>"><i class="fas fa-arrow-left"></i> Back</a>
                <div class="inbox-detail-actions">
                    <a class="btn btn-sm btn-secondary" href="<?= e($replyHref) ?>"><i class="fas fa-reply"></i> Reply</a>
                    <?php if ($active['is_read']): ?>
                    <form method="post">
                        <input type="hidden" name="action" value="unread">
                        <input type="hidden" name="id" value="<?= (int)$active['id'] ?>">
                        <input type="hidden" name="filter" value="<?= e($filter) ?>">
                        <input type="hidden" name="q" value="<?= e($q) ?>">
                        <button type="submit" class="btn btn-sm btn-secondary">Mark unread</button>
                    </form>
                    <?php else: ?>
                    <form method="post">
                        <input type="hidden" name="action" value="read">
                        <input type="hidden" name="id" value="<?= (int)$active['id'] ?>">
                        <input type="hidden" name="filter" value="<?= e($filter) ?>">
                        <input type="hidden" name="q" value="<?= e($q) ?>">
                        <button type="submit" class="btn btn-sm btn-secondary">Mark read</button>
                    </form>
                    <?php endif; ?>
                    <form method="post" onsubmit="return confirm('Delete this message permanently?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$active['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>

            <div class="inbox-detail-body">
                <h2><?= e($active['subject']) ?></h2>
                <div class="inbox-sender">
                    <div class="inbox-avatar"><?= e(mb_strtoupper(mb_substr($active['name'], 0, 1))) ?></div>
                    <div>
                        <strong><?= e($active['name']) ?></strong>
                        <a href="mailto:<?= e($active['email']) ?>"><?= e($active['email']) ?></a>
                        <time><?= e(formatDate($active['created_at'], 'M j, Y \a\t H:i')) ?></time>
                    </div>
                </div>
                <div class="inbox-message-text"><?= nl2br(e($active['message'])) ?></div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
