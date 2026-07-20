<?php
require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('blog.php');
}

$postId = (int)($_POST['post_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$comment = trim($_POST['comment'] ?? '');
$redirectTo = $_POST['redirect'] ?? url('blog.php');

if ($postId < 1 || $name === '' || $comment === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', __('form_error'));
    header('Location: ' . $redirectTo);
    exit;
}

$db = getDB();
if ($db) {
    try {
        $stmt = $db->prepare('INSERT INTO blog_comments (post_id, name, email, comment, is_approved) VALUES (?,?,?,?,0)');
        $stmt->execute([$postId, $name, $email, $comment]);
        flash('success', 'Thanks! Your comment is awaiting moderation.');
    } catch (Exception $e) {
        flash('error', __('form_error'));
    }
} else {
    flash('success', 'Thanks! Your comment was received (database offline — enable MySQL to store comments).');
}

header('Location: ' . $redirectTo);
exit;
