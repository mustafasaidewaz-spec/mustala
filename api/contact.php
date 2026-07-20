<?php
require_once dirname(__DIR__) . '/includes/init.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('contact.php');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash('error', __('form_error'));
    redirect('contact.php');
}

$db = getDB();
$saved = false;
if ($db) {
    try {
        $stmt = $db->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)');
        $stmt->execute([$name, $email, $subject, $message]);
        $saved = true;
    } catch (Exception $e) {
        $saved = false;
    }
}

// Always offer mailto fallback so the message isn't lost without a DB
$body = "Name: $name\nEmail: $email\n\n$message";
$mailto = 'mailto:' . OWNER_EMAIL
    . '?subject=' . rawurlencode($subject)
    . '&body=' . rawurlencode($body);

if ($saved) {
    flash('success', __('form_success'));
    redirect('contact.php');
}

flash('success', __('form_success'));
header('Location: ' . $mailto);
exit;
