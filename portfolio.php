<?php
/**
 * Legacy URL — redirect to the new Projects page.
 */
require_once __DIR__ . '/includes/init.php';
$qs = $_SERVER['QUERY_STRING'] ?? '';
$target = url('projects.php') . ($qs !== '' ? ('?' . $qs) : '');
header('Location: ' . $target, true, 301);
exit;
