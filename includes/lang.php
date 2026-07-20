<?php
/**
 * Language / i18n loader
 */

require_once __DIR__ . '/config.php';

function getLang(): string
{
    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS, true)) {
        $_SESSION['lang'] = $_GET['lang'];
        setcookie('mustala_lang', $_GET['lang'], time() + 60 * 60 * 24 * 365, '/');
        return $_GET['lang'];
    }
    if (!empty($_SESSION['lang']) && in_array($_SESSION['lang'], SUPPORTED_LANGS, true)) {
        return $_SESSION['lang'];
    }
    if (!empty($_COOKIE['mustala_lang']) && in_array($_COOKIE['mustala_lang'], SUPPORTED_LANGS, true)) {
        $_SESSION['lang'] = $_COOKIE['mustala_lang'];
        return $_COOKIE['mustala_lang'];
    }
    return DEFAULT_LANG;
}

function loadTranslations(): array
{
    static $translations = null;
    if ($translations !== null) {
        return $translations;
    }
    $lang = getLang();
    $enFile = BASE_PATH . '/lang/en.php';
    $en = file_exists($enFile) ? require $enFile : [];
    $file = BASE_PATH . '/lang/' . $lang . '.php';
    $translations = ($lang === 'en' || !file_exists($file))
        ? $en
        : array_merge($en, require $file);
    $contentFile = BASE_PATH . '/lang/content-' . $lang . '.php';
    if ($lang !== 'en' && file_exists($contentFile)) {
        $translations = array_merge($translations, require $contentFile);
    }
    return $translations;
}

function __(string $key, ?string $fallback = null): string
{
    $t = loadTranslations();
    if (isset($t[$key]) && $t[$key] !== '') {
        return $t[$key];
    }
    return $fallback ?? $key;
}

function getLangOptions(): array
{
    return [
        ['code' => 'en', 'label' => __('lang_en')],
        ['code' => 'pt', 'label' => __('lang_pt')],
        ['code' => 'sw', 'label' => __('lang_sw')],
    ];
}

function getFaqItems(): array
{
    $items = [];
    for ($i = 1; $i <= 8; $i++) {
        $qKey = 'faq_q' . $i;
        $aKey = 'faq_a' . $i;
        $q = __($qKey);
        if ($q === $qKey) {
            continue;
        }
        $items[] = ['q' => $q, 'a' => __($aKey)];
    }
    return $items;
}

function langUrl(string $lang): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $parts = parse_url($uri);
    $path = $parts['path'] ?? '/';
    parse_str($parts['query'] ?? '', $query);
    $query['lang'] = $lang;
    return $path . '?' . http_build_query($query);
}

function langEntityKey(string $slug): string
{
    return preg_replace('/[^a-z0-9_]/', '_', strtolower($slug));
}

function localizedField(array $row, string $field, string $entityPrefix): string
{
    $lang = getLang();
    $localizedColumn = $field . '_' . $lang;
    if (!empty($row[$localizedColumn])) {
        return (string)$row[$localizedColumn];
    }
    if ($lang !== 'en' && !empty($row[$field . '_en'])) {
        return (string)$row[$field . '_en'];
    }
    $slug = (string)($row['slug'] ?? '');
    if ($slug !== '') {
        $key = $entityPrefix . '_' . langEntityKey($slug) . '_' . $field;
        $value = __($key);
        if ($value !== $key) {
            return $value;
        }
    }
    return (string)($row[$field] ?? '');
}

function localizedCategoryName(string $name, string $slug): string
{
    if ($slug === '') {
        return $name;
    }
    $key = 'category_' . langEntityKey($slug) . '_name';
    $value = __($key);
    return $value !== $key ? $value : $name;
}

function siteBrand(): string
{
    return __('site_brand');
}

function siteTitle(string $pageTitle): string
{
    return $pageTitle . ' — ' . siteBrand();
}

function sitePageTitle(string $key): string
{
    $metaKey = 'meta_' . $key . '_title';
    $title = __($metaKey);
    if ($title !== $metaKey) {
        return $title;
    }
    $fallbackKey = $key . '_title';
    $fallback = __($fallbackKey);
    if ($fallback !== $fallbackKey) {
        return siteTitle($fallback);
    }
    return siteTitle($key);
}
