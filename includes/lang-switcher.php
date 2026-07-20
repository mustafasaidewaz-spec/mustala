<?php
/** Language dropdown — visible in header on desktop and mobile */
$currentLang = getLang();
$langOptions = getLangOptions();
$currentLabel = strtoupper($currentLang);
foreach ($langOptions as $opt) {
    if ($opt['code'] === $currentLang) {
        $currentLabel = strtoupper($opt['code']);
        break;
    }
}
?>
<div class="lang-dropdown" id="lang-dropdown">
    <button type="button"
            class="lang-dropdown-toggle"
            id="lang-toggle"
            aria-haspopup="listbox"
            aria-expanded="false"
            aria-label="<?= e(__('language')) ?>">
        <i class="fas fa-globe" aria-hidden="true"></i>
        <span class="lang-dropdown-code"><?= e($currentLabel) ?></span>
        <i class="fas fa-chevron-down lang-dropdown-caret" aria-hidden="true"></i>
    </button>
    <ul class="lang-dropdown-menu" role="listbox" hidden>
        <?php foreach ($langOptions as $opt): ?>
        <li role="option" aria-selected="<?= $currentLang === $opt['code'] ? 'true' : 'false' ?>">
            <a class="<?= $currentLang === $opt['code'] ? 'active' : '' ?>"
               href="<?= e(langUrl($opt['code'])) ?>"
               hreflang="<?= e($opt['code']) ?>">
                <span class="lang-opt-code"><?= e(strtoupper($opt['code'])) ?></span>
                <span class="lang-opt-label"><?= e($opt['label']) ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
