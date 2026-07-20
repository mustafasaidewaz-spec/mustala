<?php
require_once dirname(__DIR__) . '/includes/functions.php';
requireAdmin();

$adminPage = 'settings';
$adminTitle = 'Settings';
$adminSubtitle = 'Site identity, contact details, theme, and CV';

$db = getDB();
if (!$db) {
    require __DIR__ . '/includes/header.php';
    echo '<div class="panel"><p>Connect MySQL and import <code>database/schema.sql</code> to manage settings.</p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$themeDefaults = [
    'theme_crimson'      => '#dc143c',
    'theme_crimson_deep' => '#b01030',
    'theme_dark'         => '#111111',
    'theme_card_dark'    => '#222222',
];

$keys = [
    'site_name', 'site_tagline', 'owner_name', 'email', 'whatsapp',
    'location', 'github', 'linkedin', 'facebook', 'map_embed', 'cv_path',
    'theme_crimson', 'theme_crimson_deep', 'theme_dark', 'theme_card_dark',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($keys as $key) {
        if ($key === 'cv_path') {
            continue;
        }
        $value = trim((string)($_POST[$key] ?? ''));
        if (isset($themeDefaults[$key])) {
            $value = sanitizeHexColor($value, $themeDefaults[$key]);
        }
        setSetting($key, $value);
    }

    if (!empty($_FILES['cv_file']['name']) && ($_FILES['cv_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $cvPath = uploadFile($_FILES['cv_file'], 'docs', ['pdf', 'doc', 'docx']);
        if ($cvPath) {
            // Store site-relative path for public download links
            setSetting('cv_path', 'assets/uploads/' . $cvPath);
        } else {
            flash('error', 'CV upload failed. Use PDF/DOC/DOCX under 8MB.');
            redirect('admin/settings.php');
        }
    } elseif (isset($_POST['cv_path'])) {
        setSetting('cv_path', trim((string)$_POST['cv_path']));
    }

    flash('success', 'Settings saved successfully.');
    redirect('admin/settings.php');
}

$values = [];
foreach ($keys as $key) {
    if (isset($themeDefaults[$key])) {
        $values[$key] = sanitizeHexColor(getSetting($key, ''), $themeDefaults[$key]);
    } else {
        $values[$key] = getSetting($key, '');
    }
}

require __DIR__ . '/includes/header.php';
?>

<form method="post" enctype="multipart/form-data">
    <div class="panel">
        <h2>Site Identity</h2>
        <div class="form-grid two">
            <div>
                <label for="site_name">Site Name</label>
                <input id="site_name" name="site_name" value="<?= e($values['site_name']) ?>" required>
            </div>
            <div>
                <label for="site_tagline">Tagline</label>
                <input id="site_tagline" name="site_tagline" value="<?= e($values['site_tagline']) ?>">
            </div>
        </div>
        <div style="margin-top:1rem">
            <label for="owner_name">Owner Name</label>
            <input id="owner_name" name="owner_name" value="<?= e($values['owner_name']) ?>" required>
        </div>
    </div>

    <div class="panel">
        <h2>Contact Details</h2>
        <div class="form-grid two">
            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?= e($values['email']) ?>">
            </div>
            <div>
                <label for="whatsapp">WhatsApp (digits only, with country code)</label>
                <input id="whatsapp" name="whatsapp" value="<?= e($values['whatsapp']) ?>" placeholder="258846551778">
            </div>
        </div>
        <div style="margin-top:1rem">
            <label for="location">Location</label>
            <input id="location" name="location" value="<?= e($values['location']) ?>">
        </div>
        <div style="margin-top:1rem">
            <label for="map_embed">Google Maps Embed URL</label>
            <input id="map_embed" name="map_embed" value="<?= e($values['map_embed']) ?>">
        </div>
    </div>

    <div class="panel">
        <h2>Social Links</h2>
        <div class="form-grid">
            <div>
                <label for="github">GitHub</label>
                <input id="github" type="url" name="github" value="<?= e($values['github']) ?>">
            </div>
            <div>
                <label for="linkedin">LinkedIn</label>
                <input id="linkedin" type="url" name="linkedin" value="<?= e($values['linkedin']) ?>">
            </div>
            <div>
                <label for="facebook">Facebook</label>
                <input id="facebook" type="url" name="facebook" value="<?= e($values['facebook']) ?>">
            </div>
        </div>
    </div>

    <div class="panel">
        <h2>Theme &amp; Appearance</h2>
        <p style="color:var(--a-muted);margin-bottom:1rem;font-size:.9rem">
            Customize the public site colors. Changes apply immediately after saving.
        </p>
        <div class="theme-colors-grid">
            <div class="color-picker-field">
                <label for="theme_crimson">Primary (Crimson)</label>
                <div class="color-picker-row">
                    <input type="color" id="theme_crimson_picker" value="<?= e($values['theme_crimson']) ?>" data-sync="theme_crimson" aria-label="Primary color picker">
                    <input type="text" id="theme_crimson" name="theme_crimson" value="<?= e($values['theme_crimson']) ?>" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" maxlength="7" required>
                </div>
            </div>
            <div class="color-picker-field">
                <label for="theme_crimson_deep">Primary Hover / Deep</label>
                <div class="color-picker-row">
                    <input type="color" id="theme_crimson_deep_picker" value="<?= e($values['theme_crimson_deep']) ?>" data-sync="theme_crimson_deep" aria-label="Deep crimson picker">
                    <input type="text" id="theme_crimson_deep" name="theme_crimson_deep" value="<?= e($values['theme_crimson_deep']) ?>" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" maxlength="7" required>
                </div>
            </div>
            <div class="color-picker-field">
                <label for="theme_dark">Dark Background / Navbar</label>
                <div class="color-picker-row">
                    <input type="color" id="theme_dark_picker" value="<?= e($values['theme_dark']) ?>" data-sync="theme_dark" aria-label="Dark color picker">
                    <input type="text" id="theme_dark" name="theme_dark" value="<?= e($values['theme_dark']) ?>" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" maxlength="7" required>
                </div>
            </div>
            <div class="color-picker-field">
                <label for="theme_card_dark">Card Dark Background</label>
                <div class="color-picker-row">
                    <input type="color" id="theme_card_dark_picker" value="<?= e($values['theme_card_dark']) ?>" data-sync="theme_card_dark" aria-label="Card dark picker">
                    <input type="text" id="theme_card_dark" name="theme_card_dark" value="<?= e($values['theme_card_dark']) ?>" pattern="#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})" maxlength="7" required>
                </div>
            </div>
        </div>
        <div class="theme-preview" aria-hidden="true">
            <span class="swatch" style="background:<?= e($values['theme_crimson']) ?>">Primary</span>
            <span class="swatch" style="background:<?= e($values['theme_crimson_deep']) ?>">Deep</span>
            <span class="swatch" style="background:<?= e($values['theme_dark']) ?>">Dark</span>
            <span class="swatch" style="background:<?= e($values['theme_card_dark']) ?>">Cards</span>
        </div>
    </div>

    <div class="panel">
        <h2>Resume / CV</h2>
        <p style="color:var(--a-muted);margin-bottom:1rem;font-size:.9rem">
            Current file:
            <?php if ($values['cv_path']): ?>
                <?php
                $cvHref = $values['cv_path'];
                if (!str_starts_with($cvHref, 'http') && !str_starts_with($cvHref, '/')) {
                    $cvHref = url(ltrim($cvHref, '/'));
                }
                ?>
                <a href="<?= e($cvHref) ?>" target="_blank" rel="noopener" style="color:var(--a-crimson);font-weight:600">
                    <?= e($values['cv_path']) ?>
                </a>
            <?php else: ?>
                <em>Not set</em>
            <?php endif; ?>
        </p>
        <div class="form-grid two">
            <div>
                <label for="cv_path">CV path (manual)</label>
                <input id="cv_path" name="cv_path" value="<?= e($values['cv_path']) ?>" placeholder="assets/uploads/docs/cv.pdf">
            </div>
            <div>
                <label>Or upload a new CV</label>
                <div class="upload-zone" data-upload-zone data-accept=".pdf,.doc,.docx,application/pdf" data-multiple="false">
                    <input type="file" name="cv_file" accept=".pdf,.doc,.docx,application/pdf" data-upload-input>
                    <div class="upload-zone-inner">
                        <i class="fas fa-file-arrow-up"></i>
                        <strong>Drag & drop CV here</strong>
                        <span>PDF, DOC, DOCX · max 8MB</span>
                        <button type="button" class="btn btn-secondary btn-sm" data-upload-browse>Browse file</button>
                    </div>
                    <div class="upload-preview" data-upload-preview></div>
                </div>
            </div>
        </div>
    </div>

    <div class="actions" style="margin-bottom:2rem">
        <button class="btn" type="submit"><i class="fas fa-save"></i> Save Settings</button>
        <a class="btn btn-secondary" href="<?= url('admin/index.php') ?>">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
