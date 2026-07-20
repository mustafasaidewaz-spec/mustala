<?php
/**
 * Shared helper functions
 */

require_once __DIR__ . '/db.php';

function e(?string $str): string
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return BASE_URL . '/' . $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function currentPage(): string
{
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php');
    return $script === 'index' ? 'home' : $script;
}

function isActive(string $page): string
{
    return currentPage() === $page ? 'active' : '';
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . time();
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function getSetting(string $key, string $default = ''): string
{
    if (!isset($GLOBALS['__mustala_settings_cache']) || !is_array($GLOBALS['__mustala_settings_cache'])) {
        $GLOBALS['__mustala_settings_cache'] = [];
    }
    $cache = &$GLOBALS['__mustala_settings_cache'];
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    $db = getDB();
    if (!$db) {
        return $default;
    }
    try {
        $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $row = $stmt->fetch();
        $cache[$key] = $row ? (string)$row['setting_value'] : $default;
        return $cache[$key];
    } catch (Exception $e) {
        return $default;
    }
}

function setSetting(string $key, string $value): bool
{
    $db = getDB();
    if (!$db) {
        return false;
    }
    try {
        $stmt = $db->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        $ok = $stmt->execute([$key, $value]);
        if (!isset($GLOBALS['__mustala_settings_cache']) || !is_array($GLOBALS['__mustala_settings_cache'])) {
            $GLOBALS['__mustala_settings_cache'] = [];
        }
        $GLOBALS['__mustala_settings_cache'][$key] = $value;
        return $ok;
    } catch (Exception $e) {
        return false;
    }
}

function mustalaDefineSiteConstants(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    global $MUSTALA_SITE_DEFAULTS;
    $map = [
        'SITE_NAME'      => 'site_name',
        'SITE_TAGLINE'   => 'site_tagline',
        'OWNER_NAME'     => 'owner_name',
        'OWNER_EMAIL'    => 'email',
        'OWNER_WHATSAPP' => 'whatsapp',
        'OWNER_LOCATION' => 'location',
        'OWNER_GITHUB'   => 'github',
        'OWNER_LINKEDIN' => 'linkedin',
        'OWNER_FACEBOOK' => 'facebook',
    ];

    foreach ($MUSTALA_SITE_DEFAULTS as $const => $fallback) {
        if (defined($const)) {
            continue;
        }
        $value = getSetting($map[$const] ?? '', $fallback);
        if ($value === '') {
            $value = $fallback;
        }
        define($const, $value);
    }
}

function sanitizeHexColor(string $value, string $fallback): string
{
    $value = trim($value);
    if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value)) {
        if (strlen($value) === 4) {
            return sprintf(
                '#%s%s%s',
                str_repeat($value[1], 2),
                str_repeat($value[2], 2),
                str_repeat($value[3], 2)
            );
        }
        return strtolower($value);
    }
    return $fallback;
}

function getThemeSettings(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [
        'crimson'      => sanitizeHexColor(getSetting('theme_crimson', ''), '#dc143c'),
        'crimson_deep' => sanitizeHexColor(getSetting('theme_crimson_deep', ''), '#b01030'),
        'dark'         => sanitizeHexColor(getSetting('theme_dark', ''), '#111111'),
        'card_dark'    => sanitizeHexColor(getSetting('theme_card_dark', ''), '#222222'),
    ];
    return $cache;
}

function renderThemeStyles(): string
{
    $t = getThemeSettings();
    return '<style id="mustala-theme">:root{'
        . '--crimson:' . $t['crimson'] . ';'
        . '--crimson-deep:' . $t['crimson_deep'] . ';'
        . '--dark:' . $t['dark'] . ';'
        . '--card-dark:' . $t['card_dark'] . ';'
        . '}</style>';
}

function renderAdminThemeStyles(): string
{
    $t = getThemeSettings();
    return '<style id="mustala-admin-theme">:root{'
        . '--a-crimson:' . $t['crimson'] . ';'
        . '--a-sidebar:' . $t['dark'] . ';'
        . '}</style>';
}

function truncate(string $text, int $length = 120): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . '…';
}

function formatDate(string $date, string $format = 'M j, Y'): string
{
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    if ($format !== 'M j, Y') {
        return date($format, $ts);
    }
    $lang = getLang();
    if (class_exists('IntlDateFormatter')) {
        $locale = match ($lang) {
            'pt' => 'pt_PT',
            'sw' => 'sw_KE',
            default => 'en_US',
        };
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
        $formatted = $formatter->format($ts);
        if (is_string($formatted) && $formatted !== '') {
            return $formatted;
        }
    }
    return match ($lang) {
        'pt' => date('d/m/Y', $ts),
        'sw' => date('d M Y', $ts),
        default => date('M j, Y', $ts),
    };
}

function localizeProject(array $row): array
{
    foreach (['title', 'description', 'content'] as $field) {
        $row[$field] = localizedField($row, $field, 'project');
    }
    if (!empty($row['category_slug'])) {
        $row['category_name'] = localizedCategoryName((string)($row['category_name'] ?? ''), (string)$row['category_slug']);
    }
    return $row;
}

function localizePost(array $row): array
{
    foreach (['title', 'excerpt', 'content'] as $field) {
        $row[$field] = localizedField($row, $field, 'post');
    }
    if (!empty($row['category_slug'])) {
        $row['category_name'] = localizedCategoryName((string)($row['category_name'] ?? ''), (string)$row['category_slug']);
    }
    return $row;
}

function localizeCategory(array $row): array
{
    $row['name'] = localizedCategoryName((string)($row['name'] ?? ''), (string)($row['slug'] ?? ''));
    return $row;
}

function localizeGalleryItem(array $row): array
{
    $id = (int)($row['id'] ?? 0);
    if ($id > 0) {
        foreach (['title', 'description'] as $field) {
            $key = 'gallery_item_' . $id . '_' . $field;
            $value = __($key);
            if ($value !== $key) {
                $row[$field] = $value;
            }
        }
    }
    if (!empty($row['category_slug'])) {
        $row['category_name'] = localizedCategoryName((string)($row['category_name'] ?? ''), (string)$row['category_slug']);
    }
    return $row;
}

function stars(int $rating): string
{
    $rating = max(1, min(5, $rating));
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= $i <= $rating
            ? '<i class="fas fa-star"></i>'
            : '<i class="far fa-star"></i>';
    }
    return $html;
}

function uploadMaxBytes(string $ext): int
{
    $video = ['mp4', 'webm', 'ogg', 'mov'];
    return in_array($ext, $video, true) ? 50 * 1024 * 1024 : 8 * 1024 * 1024;
}

function uploadFile(array $file, string $folder, array $allowed = ['jpg','jpeg','png','gif','webp','mp4','webm']): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $ext = preg_replace('/[^a-z0-9]/', '', $ext) ?? '';
    if ($ext === '' || !in_array($ext, $allowed, true)) {
        return null;
    }
    $size = (int)($file['size'] ?? 0);
    if ($size < 1 || $size > uploadMaxBytes($ext)) {
        return null;
    }
    $tmp = $file['tmp_name'] ?? '';
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        return null;
    }
    $imageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $imageExt, true)) {
        $info = @getimagesize($tmp);
        if ($info === false) {
            return null;
        }
    }
    $dir = UPLOAD_PATH . '/' . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
    $dest = $dir . '/' . $filename;
    if (move_uploaded_file($tmp, $dest)) {
        @chmod($dest, 0644);
        return $folder . '/' . $filename;
    }
    return null;
}

/**
 * Normalize $_FILES['field'] whether single or multiple.
 * @return array<int, array{name:string,type:string,tmp_name:string,error:int,size:int}>
 */
function normalizeUploadFiles(array $filesField): array
{
    if (!isset($filesField['name'])) {
        return [];
    }
    if (!is_array($filesField['name'])) {
        return [[
            'name' => (string)$filesField['name'],
            'type' => (string)($filesField['type'] ?? ''),
            'tmp_name' => (string)($filesField['tmp_name'] ?? ''),
            'error' => (int)($filesField['error'] ?? UPLOAD_ERR_NO_FILE),
            'size' => (int)($filesField['size'] ?? 0),
        ]];
    }
    $out = [];
    foreach ($filesField['name'] as $i => $name) {
        $out[] = [
            'name' => (string)$name,
            'type' => (string)($filesField['type'][$i] ?? ''),
            'tmp_name' => (string)($filesField['tmp_name'][$i] ?? ''),
            'error' => (int)($filesField['error'][$i] ?? UPLOAD_ERR_NO_FILE),
            'size' => (int)($filesField['size'][$i] ?? 0),
        ];
    }
    return $out;
}

/**
 * @return array{uploaded: string[], failed: int}
 */
function uploadFiles(array $filesField, string $folder, array $allowed = ['jpg','jpeg','png','gif','webp','mp4','webm']): array
{
    $uploaded = [];
    $failed = 0;
    foreach (normalizeUploadFiles($filesField) as $file) {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $path = uploadFile($file, $folder, $allowed);
        if ($path) {
            $uploaded[] = $path;
        } else {
            $failed++;
        }
    }
    return ['uploaded' => $uploaded, 'failed' => $failed];
}

function mediaUrl(?string $path, string $fallback = 'img/placeholder.svg'): string
{
    if ($path && file_exists(UPLOAD_PATH . '/' . $path)) {
        return UPLOAD_URL . '/' . $path;
    }
    if ($path && str_starts_with($path, 'http')) {
        return $path;
    }
    return asset($fallback);
}

function isAdminLoggedIn(): bool
{
    return !empty($_SESSION['admin_id']);
}

function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: ' . url('admin/login.php'));
        exit;
    }
}

/* ---------- Queries with fallbacks ---------- */

function projectPublicPath(?string $path): string
{
    if (!$path) {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    if (str_starts_with($path, 'assets/')) {
        return $path;
    }
    if (file_exists(UPLOAD_PATH . '/' . ltrim($path, '/'))) {
        return 'assets/uploads/' . ltrim($path, '/');
    }
    return ltrim($path, '/');
}

function parseProjectVideos(?string $url): array
{
    $url = trim((string)$url);
    if ($url === '') {
        return [];
    }
    if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
        return [['type' => 'youtube', 'src' => $m[1], 'title' => 'Project video']];
    }
    if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
        return [['type' => 'youtube', 'src' => $url, 'title' => 'Project video']];
    }
    return [['type' => 'file', 'src' => projectPublicPath($url), 'title' => 'Project video']];
}

function projectLinkIsValid(?string $url): bool
{
    $url = trim((string)$url);
    return $url !== '' && $url !== '#';
}

function projectYoutubeEmbed(?string $url): ?string
{
    $videos = parseProjectVideos($url);
    if (!$videos || ($videos[0]['type'] ?? '') !== 'youtube') {
        return null;
    }
    return 'https://www.youtube.com/embed/' . $videos[0]['src'];
}

function projectFeatureList(?string $content): array
{
    if (!$content || !preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $content, $matches)) {
        return [];
    }
    return array_values(array_filter(array_map(static function ($item) {
        return trim(strip_tags($item));
    }, $matches[1])));
}

function isImageMediaPath(?string $path): bool
{
    $path = strtolower(trim((string)$path));
    if ($path === '') {
        return false;
    }
    if (preg_match('/(?:youtube\.com|youtu\.be|vimeo\.com)/', $path)) {
        return false;
    }
    $ext = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $path, PATHINFO_EXTENSION));
    $videoExt = ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'avi', 'mkv', 'm4v', 'wmv'];
    return !in_array($ext, $videoExt, true);
}

function projectGalleryImages(array $rows, ?string $excludeUrl = null): array
{
    $out = [];
    foreach ($rows as $img) {
        $path = (string)($img['image_path'] ?? '');
        if (!isImageMediaPath($path)) {
            continue;
        }
        $shot = mediaUrl($path);
        if ($excludeUrl !== null && $shot === $excludeUrl) {
            continue;
        }
        $out[] = $img;
    }
    return $out;
}

function projectUniqueGalleryImages(array $rows, ?string $featuredPath = null): array
{
    $out = [];
    $seen = [];

    if ($featuredPath !== null && $featuredPath !== '' && isImageMediaPath($featuredPath)) {
        $out[] = ['image_path' => $featuredPath];
        $seen[mediaUrl($featuredPath)] = true;
    }

    foreach ($rows as $img) {
        $path = (string)($img['image_path'] ?? '');
        if (!isImageMediaPath($path)) {
            continue;
        }
        $url = mediaUrl($path);
        if (isset($seen[$url])) {
            continue;
        }
        $seen[$url] = true;
        $out[] = $img;
    }

    return $out;
}

function mapProjectRowToJson(array $row, array $screenshots = []): array
{
    $row = localizeProject($row);
    $thumb = projectPublicPath($row['featured_image'] ?? null) ?: 'assets/img/placeholder.svg';
    $images = array_values(array_filter(array_map('projectPublicPath', $screenshots)));
    if (!$images) {
        $images = [$thumb];
    }

    $tech = array_values(array_filter(array_map('trim', explode(',', (string)($row['tech_stack'] ?? '')))));
    $features = [];
    if (!empty($row['content']) && preg_match_all('/<li[^>]*>(.*?)<\/li>/is', (string)$row['content'], $matches)) {
        $features = array_values(array_filter(array_map(static function ($item) {
            return trim(strip_tags($item));
        }, $matches[1])));
    }

    $description = trim(strip_tags((string)($row['description'] ?? '')));
    $short = truncate($description !== '' ? $description : (string)($row['title'] ?? ''), 160);

    return [
        'id' => (string)($row['id'] ?? $row['slug'] ?? ''),
        'slug' => (string)($row['slug'] ?? ''),
        'title' => (string)($row['title'] ?? ''),
        'category' => (string)($row['category_slug'] ?? 'uncategorized'),
        'thumbnail' => $thumb,
        'shortDescription' => $short,
        'description' => $description,
        'features' => $features,
        'technologies' => $tech,
        'clientType' => '',
        'completedAt' => !empty($row['created_at']) ? date('Y-m', strtotime((string)$row['created_at'])) : '',
        'liveDemo' => (string)($row['live_demo'] ?? ''),
        'github' => (string)($row['github_url'] ?? ''),
        'images' => $images,
        'videos' => parseProjectVideos($row['video_url'] ?? null),
        'detailUrl' => url('project.php?slug=' . urlencode((string)($row['slug'] ?? ''))),
    ];
}

function getProjectsJsonPayload(): array
{
    $categories = [['id' => 'all', 'label' => __('filter_all')]];
    foreach (getCategories('portfolio') as $cat) {
        $categories[] = ['id' => $cat['slug'], 'label' => $cat['name']];
    }

    $projects = [];
    $db = getDB();
    if ($db) {
        try {
            $stmt = $db->query(
                "SELECT p.*, c.name AS category_name, c.slug AS category_slug
                 FROM projects p
                 LEFT JOIN categories c ON c.id = p.category_id
                 WHERE p.status = 'published'
                 ORDER BY p.is_featured DESC, p.created_at DESC"
            );
            $rows = $stmt->fetchAll();
            $imgStmt = $db->prepare('SELECT image_path FROM project_images WHERE project_id = ? ORDER BY sort_order');
            foreach ($rows as $row) {
                $imgStmt->execute([(int)$row['id']]);
                $screenshots = array_column($imgStmt->fetchAll(), 'image_path');
                $projects[] = mapProjectRowToJson($row, $screenshots);
            }
        } catch (Exception $e) {
            $projects = [];
        }
    }

    if (!$projects) {
        foreach (sampleProjects(50) as $row) {
            $projects[] = mapProjectRowToJson($row);
        }
    }

    return ['categories' => $categories, 'projects' => $projects];
}

function getFeaturedProjects(int $limit = 3): array
{
    $db = getDB();
    if (!$db) {
        return sampleProjects($limit);
    }
    try {
        $stmt = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM projects p
            LEFT JOIN categories c ON c.id = p.category_id
            WHERE p.status='published' ORDER BY p.is_featured DESC, p.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = array_map('localizeProject', $stmt->fetchAll());
        return $rows ?: array_map('localizeProject', sampleProjects($limit));
    } catch (Exception $e) {
        return sampleProjects($limit);
    }
}

function getProjects(?string $category = null, ?string $search = null, int $page = 1, int $perPage = PROJECTS_PER_PAGE): array
{
    $db = getDB();
    if (!$db) {
        return ['items' => sampleProjects($perPage), 'total' => 4, 'pages' => 1];
    }
    try {
        $where = ["p.status='published'"];
        $params = [];
        if ($category) {
            $where[] = 'c.slug = ?';
            $params[] = $category;
        }
        if ($search) {
            $where[] = '(p.title LIKE ? OR p.description LIKE ? OR p.tech_stack LIKE ?)';
            $q = '%' . $search . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }
        $sqlWhere = implode(' AND ', $where);
        $countStmt = $db->prepare("SELECT COUNT(*) FROM projects p LEFT JOIN categories c ON c.id=p.category_id WHERE $sqlWhere");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = max(1, (int)ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM projects p
            LEFT JOIN categories c ON c.id=p.category_id WHERE $sqlWhere
            ORDER BY p.created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        return ['items' => array_map('localizeProject', $stmt->fetchAll()), 'total' => $total, 'pages' => $pages];
    } catch (Exception $e) {
        return ['items' => array_map('localizeProject', sampleProjects($perPage)), 'total' => 4, 'pages' => 1];
    }
}

function getProjectBySlug(string $slug): ?array
{
    $db = getDB();
    if (!$db) {
        foreach (sampleProjects(10) as $p) {
            if ($p['slug'] === $slug) {
                return localizeProject($p);
            }
        }
        return null;
    }
    try {
        $stmt = $db->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM projects p
            LEFT JOIN categories c ON c.id=p.category_id WHERE p.slug=? AND p.status='published'");
        $stmt->execute([$slug]);
        $project = $stmt->fetch();
        if ($project) {
            $project = localizeProject($project);
            $db->prepare('UPDATE projects SET views = views + 1 WHERE id=?')->execute([$project['id']]);
            $img = $db->prepare('SELECT * FROM project_images WHERE project_id=? ORDER BY sort_order');
            $img->execute([$project['id']]);
            $project['images'] = $img->fetchAll();
        }
        return $project ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getFeaturedPosts(int $limit = 3): array
{
    $db = getDB();
    if (!$db) {
        return samplePosts($limit);
    }
    try {
        $stmt = $db->prepare("SELECT b.*, c.name AS category_name FROM blog_posts b
            LEFT JOIN categories c ON c.id=b.category_id
            WHERE b.status='published' ORDER BY b.is_featured DESC, b.created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = array_map('localizePost', $stmt->fetchAll());
        return $rows ?: array_map('localizePost', samplePosts($limit));
    } catch (Exception $e) {
        return samplePosts($limit);
    }
}

function getPosts(?string $category = null, ?string $tag = null, ?string $search = null, int $page = 1, int $perPage = POSTS_PER_PAGE): array
{
    $db = getDB();
    if (!$db) {
        return ['items' => samplePosts($perPage), 'total' => 3, 'pages' => 1];
    }
    try {
        $where = ["b.status='published'"];
        $params = [];
        if ($category) {
            $where[] = 'c.slug = ?';
            $params[] = $category;
        }
        if ($tag) {
            $where[] = 'b.tags LIKE ?';
            $params[] = '%' . $tag . '%';
        }
        if ($search) {
            $where[] = '(b.title LIKE ? OR b.excerpt LIKE ? OR b.content LIKE ?)';
            $q = '%' . $search . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }
        $sqlWhere = implode(' AND ', $where);
        $countStmt = $db->prepare("SELECT COUNT(*) FROM blog_posts b LEFT JOIN categories c ON c.id=b.category_id WHERE $sqlWhere");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = max(1, (int)ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT b.*, c.name AS category_name, c.slug AS category_slug FROM blog_posts b
            LEFT JOIN categories c ON c.id=b.category_id WHERE $sqlWhere
            ORDER BY b.created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        return ['items' => array_map('localizePost', $stmt->fetchAll()), 'total' => $total, 'pages' => $pages];
    } catch (Exception $e) {
        return ['items' => array_map('localizePost', samplePosts($perPage)), 'total' => 3, 'pages' => 1];
    }
}

function getPostBySlug(string $slug): ?array
{
    $db = getDB();
    if (!$db) {
        foreach (samplePosts(10) as $p) {
            if ($p['slug'] === $slug) {
                return localizePost($p);
            }
        }
        return null;
    }
    try {
        $stmt = $db->prepare("SELECT b.*, c.name AS category_name, c.slug AS category_slug FROM blog_posts b
            LEFT JOIN categories c ON c.id=b.category_id WHERE b.slug=? AND b.status='published'");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
        if ($post) {
            $post = localizePost($post);
            $db->prepare('UPDATE blog_posts SET views = views + 1 WHERE id=?')->execute([$post['id']]);
        }
        return $post ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getRelatedPosts(int $postId, ?int $categoryId, int $limit = 3): array
{
    $db = getDB();
    if (!$db) {
        return samplePosts($limit);
    }
    try {
        if ($categoryId) {
            $stmt = $db->prepare("SELECT b.*, c.name AS category_name FROM blog_posts b
                LEFT JOIN categories c ON c.id=b.category_id
                WHERE b.status='published' AND b.id != ? AND b.category_id = ?
                ORDER BY b.created_at DESC LIMIT ?");
            $stmt->bindValue(1, $postId, PDO::PARAM_INT);
            $stmt->bindValue(2, $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        } else {
            $stmt = $db->prepare("SELECT b.*, c.name AS category_name FROM blog_posts b
                LEFT JOIN categories c ON c.id=b.category_id
                WHERE b.status='published' AND b.id != ?
                ORDER BY b.created_at DESC LIMIT ?");
            $stmt->bindValue(1, $postId, PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return array_map('localizePost', $stmt->fetchAll());
    } catch (Exception $e) {
        return [];
    }
}

function getComments(int $postId): array
{
    $db = getDB();
    if (!$db) {
        return [];
    }
    try {
        $stmt = $db->prepare("SELECT * FROM blog_comments WHERE post_id=? AND is_approved=1 ORDER BY created_at DESC");
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function getTestimonials(int $limit = 6): array
{
    $db = getDB();
    if (!$db) {
        return sampleTestimonials($limit);
    }
    try {
        $stmt = $db->prepare("SELECT * FROM testimonials WHERE status='published' ORDER BY is_featured DESC, created_at DESC LIMIT ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $rows = array_map('localizeTestimonial', $rows);
        return $rows ?: sampleTestimonials($limit);
    } catch (Exception $e) {
        return sampleTestimonials($limit);
    }
}

function localizeTestimonial(array $row): array
{
    $id = (int)($row['id'] ?? 0);
    if ($id < 1) {
        return $row;
    }

    $prefix = 'testimonial' . $id . '_';
    foreach (['content', 'role', 'company', 'client_name'] as $field) {
        $key = $prefix . ($field === 'client_name' ? 'name' : $field);
        $value = __($key);
        if ($value !== $key) {
            $row[$field] = $value;
        }
    }

    return $row;
}

function getGallery(?string $category = null, ?string $type = null, int $page = 1, int $perPage = GALLERY_PER_PAGE): array
{
    $db = getDB();
    if (!$db) {
        return ['items' => [], 'total' => 0, 'pages' => 1];
    }
    try {
        $where = ['1=1'];
        $params = [];
        if ($category) {
            $where[] = 'c.slug = ?';
            $params[] = $category;
        }
        if ($type && in_array($type, ['image', 'video'], true)) {
            $where[] = 'g.media_type = ?';
            $params[] = $type;
        }
        $sqlWhere = implode(' AND ', $where);
        $countStmt = $db->prepare("SELECT COUNT(*) FROM gallery g LEFT JOIN categories c ON c.id=g.category_id WHERE $sqlWhere");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();
        $pages = max(1, (int)ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT g.*, c.name AS category_name, c.slug AS category_slug FROM gallery g
            LEFT JOIN categories c ON c.id=g.category_id WHERE $sqlWhere
            ORDER BY g.sort_order, g.created_at DESC LIMIT $perPage OFFSET $offset");
        $stmt->execute($params);
        return ['items' => array_map('localizeGalleryItem', $stmt->fetchAll()), 'total' => $total, 'pages' => $pages];
    } catch (Exception $e) {
        return ['items' => [], 'total' => 0, 'pages' => 1];
    }
}

function getCategories(string $type): array
{
    $db = getDB();
    if (!$db) {
        return [];
    }
    try {
        $stmt = $db->prepare('SELECT * FROM categories WHERE type=? ORDER BY name');
        $stmt->execute([$type]);
        return array_map('localizeCategory', $stmt->fetchAll());
    } catch (Exception $e) {
        return [];
    }
}

function getSkillBarItems(): array
{
    return [
        ['name' => 'HTML', 'level' => 90],
        ['name' => 'CSS', 'level' => 85],
        ['name' => 'JavaScript', 'level' => 70],
        ['name' => 'WordPress', 'level' => 88],
        ['name' => 'PHP', 'level' => 55],
        ['name' => 'MySQL', 'level' => 50],
    ];
}

function getExperienceTimeline(): array
{
    return [
        ['year' => __('exp1_year'), 'title' => __('exp1_title'), 'desc' => __('exp1_desc')],
        ['year' => __('exp2_year'), 'title' => __('exp2_title'), 'desc' => __('exp2_desc')],
        ['year' => __('exp3_year'), 'title' => __('exp3_title'), 'desc' => __('exp3_desc')],
    ];
}

function getEducationTimeline(): array
{
    return [
        ['year' => __('edu1_year'), 'title' => __('edu1_title'), 'desc' => __('edu1_desc')],
        ['year' => __('edu2_year'), 'title' => __('edu2_title'), 'desc' => __('edu2_desc')],
    ];
}

function getCertificateList(): array
{
    return [__('cert1'), __('cert2'), __('cert3')];
}

function getHomeResumeItems(): array
{
    return [
        ['period' => __('resume1_period'), 'title' => __('resume1_title'), 'place' => __('resume1_place'), 'desc' => __('resume1_desc')],
        ['period' => __('resume2_period'), 'title' => __('resume2_title'), 'place' => __('resume2_place'), 'desc' => __('resume2_desc')],
        ['period' => __('resume3_period'), 'title' => __('resume3_title'), 'place' => __('resume3_place'), 'desc' => __('resume3_desc')],
    ];
}

function getTeamMembers(string $aboutImg, string $team2, string $team3): array
{
    return [
        ['name' => __('team1_name'), 'bio' => __('team1_bio'), 'image' => $aboutImg],
        ['name' => __('team2_name'), 'bio' => __('team2_bio'), 'image' => $team2],
        ['name' => __('team3_name'), 'bio' => __('team3_bio'), 'image' => $team3],
    ];
}

function getTypedRoleStrings(): array
{
    return [
        __('typed_role_1'),
        __('typed_role_2'),
        __('typed_role_3'),
        __('typed_role_4'),
    ];
}

function getTypedRoleStringsAlt(): array
{
    return [
        __('typed_role_1'),
        __('typed_role_2'),
        __('typed_role_5'),
        __('typed_role_4'),
    ];
}

function sampleProjects(int $limit = 3): array
{
    $all = [
        ['id' => 1, 'title' => 'Modern Business Website', 'slug' => 'modern-business-website', 'description' => 'Responsive business website with clean UI and contact integration.', 'content' => '<p>A fully responsive business website built for a local client.</p>', 'category_name' => 'Frontend', 'category_slug' => 'frontend', 'featured_image' => null, 'video_url' => null, 'tech_stack' => 'HTML, CSS, JavaScript, PHP', 'live_demo' => '#', 'github_url' => '#', 'images' => []],
        ['id' => 2, 'title' => 'WooCommerce Online Store', 'slug' => 'woocommerce-online-store', 'description' => 'Complete e-commerce store with product management and payments.', 'content' => '<p>Custom WooCommerce store with product catalog and payments.</p>', 'category_name' => 'E-commerce', 'category_slug' => 'ecommerce', 'featured_image' => null, 'video_url' => null, 'tech_stack' => 'WordPress, WooCommerce, PHP, MySQL', 'live_demo' => '#', 'github_url' => '#', 'images' => []],
        ['id' => 3, 'title' => 'Landing Page for Startup', 'slug' => 'landing-page-startup', 'description' => 'High-converting landing page designed for lead generation.', 'content' => '<p>Marketing landing page optimized for conversions.</p>', 'category_name' => 'Landing Pages', 'category_slug' => 'landing-pages', 'featured_image' => null, 'video_url' => null, 'tech_stack' => 'HTML, CSS, JavaScript', 'live_demo' => '#', 'github_url' => '#', 'images' => []],
        ['id' => 4, 'title' => 'WordPress Corporate Site', 'slug' => 'wordpress-corporate-site', 'description' => 'Custom WordPress theme for a corporate client.', 'content' => '<p>Custom theme with editable sections and blog.</p>', 'category_name' => 'WordPress', 'category_slug' => 'wordpress-projects', 'featured_image' => null, 'video_url' => null, 'tech_stack' => 'WordPress, PHP, CSS, JS', 'live_demo' => '#', 'github_url' => '#', 'images' => []],
    ];
    return array_slice(array_map('localizeProject', $all), 0, $limit);
}

function samplePosts(int $limit = 3): array
{
    $all = [
        ['id' => 1, 'title' => 'How to Build a Fast Responsive Website', 'slug' => 'build-fast-responsive-website', 'excerpt' => 'Practical tips for creating websites that load quickly and look great on every device.', 'content' => '<p>Performance and responsiveness are essential for modern websites.</p>', 'category_name' => 'Development', 'category_slug' => 'development', 'tags' => 'performance,responsive,html,css', 'featured_image' => null, 'video_url' => null, 'author' => 'Mustafa Saide', 'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')), 'views' => 120],
        ['id' => 2, 'title' => 'Why WordPress Is Great for Small Business', 'slug' => 'wordpress-for-small-business', 'excerpt' => 'Explore why WordPress remains one of the best platforms for small business websites.', 'content' => '<p>WordPress is flexible, SEO-friendly, and easy to manage.</p>', 'category_name' => 'WordPress', 'category_slug' => 'wordpress', 'tags' => 'wordpress,business,cms', 'featured_image' => null, 'video_url' => null, 'author' => 'Mustafa Saide', 'created_at' => date('Y-m-d H:i:s', strtotime('-7 days')), 'views' => 89],
        ['id' => 3, 'title' => 'Freelance Web Development Tips for 2026', 'slug' => 'freelance-web-dev-tips-2026', 'excerpt' => 'Advice for freelancers looking to grow their web development career this year.', 'content' => '<p>Build a strong portfolio and communicate clearly with clients.</p>', 'category_name' => 'Freelance Tips', 'category_slug' => 'freelance-tips', 'tags' => 'freelance,career,tips', 'featured_image' => null, 'video_url' => null, 'author' => 'Mustafa Saide', 'created_at' => date('Y-m-d H:i:s', strtotime('-14 days')), 'views' => 64],
    ];
    return array_slice(array_map('localizePost', $all), 0, $limit);
}

function sampleTestimonials(int $limit = 3): array
{
    $all = [];
    for ($i = 1; $i <= 3; $i++) {
        $all[] = [
            'id' => $i,
            'client_name' => __('testimonial' . $i . '_name'),
            'company' => __('testimonial' . $i . '_company'),
            'role' => __('testimonial' . $i . '_role'),
            'content' => __('testimonial' . $i . '_content'),
            'rating' => $i === 3 ? 4 : 5,
            'client_image' => null,
            'company_logo' => null,
        ];
    }
    return array_slice($all, 0, $limit);
}

function pagination(int $current, int $totalPages, string $baseUrl): string
{
    if ($totalPages <= 1) {
        return '';
    }
    $html = '<nav class="pagination" aria-label="' . e(__('aria_pagination')) . '"><ul>';
    for ($i = 1; $i <= $totalPages; $i++) {
        $sep = str_contains($baseUrl, '?') ? '&' : '?';
        $href = e($baseUrl . $sep . 'page=' . $i);
        $active = $i === $current ? ' class="active"' : '';
        $html .= "<li{$active}><a href=\"{$href}\">{$i}</a></li>";
    }
    $html .= '</ul></nav>';
    return $html;
}

mustalaDefineSiteConstants();
