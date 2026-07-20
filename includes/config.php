<?php
/**
 * Mustala Portfolio — site configuration
 */

$MUSTALA_SITE_DEFAULTS = [
    'SITE_NAME'      => 'Mustala',
    'SITE_TAGLINE'   => 'Freelance Web Developer',
    'OWNER_NAME'     => 'Mustafa Saide',
    'OWNER_EMAIL'    => 'mustafasaidewaz@gmail.com',
    'OWNER_WHATSAPP' => '258846551778',
    'OWNER_LOCATION' => 'Palma, Mozambique',
    'OWNER_GITHUB'   => 'https://github.com/',
    'OWNER_LINKEDIN' => 'https://www.linkedin.com/in/mustafa-saide-a88090290',
    'OWNER_FACEBOOK' => 'https://www.facebook.com/profile.php?id=61554800685289',
];

define('DB_HOST', 'localhost');
define('DB_NAME', 'mustala_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('BASE_PATH', dirname(__DIR__));

// Auto-detect subfolder (e.g. /Mustala). Override by setting $FORCE_BASE_URL before including config.
if (!defined('BASE_URL')) {
    $force = $FORCE_BASE_URL ?? null;
    if ($force !== null) {
        define('BASE_URL', rtrim((string)$force, '/'));
    } else {
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
        $appRoot = str_replace('\\', '/', realpath(BASE_PATH));
        $auto = '';
        if ($docRoot && $appRoot && str_starts_with($appRoot, $docRoot)) {
            $auto = substr($appRoot, strlen($docRoot));
        }
        define('BASE_URL', $auto === false ? '' : rtrim($auto, '/'));
    }
}

define('UPLOAD_PATH', BASE_PATH . '/assets/uploads');
define('UPLOAD_URL', BASE_URL . '/assets/uploads');

define('POSTS_PER_PAGE', 6);
define('PROJECTS_PER_PAGE', 9);
define('GALLERY_PER_PAGE', 12);

define('DEFAULT_LANG', 'en');
define('SUPPORTED_LANGS', ['en', 'pt', 'sw']);

date_default_timezone_set('Africa/Maputo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
