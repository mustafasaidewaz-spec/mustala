<?php
require_once dirname(__DIR__) . '/includes/init.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

echo json_encode(getProjectsJsonPayload(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
