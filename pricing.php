<?php
require_once __DIR__ . '/includes/init.php';
$pageTitle = sitePageTitle('pricing');
$pageDescription = __('pricing_sub');
require_once __DIR__ . '/includes/header.php';

$packages = [
    [
        'name' => __('pkg_starter'),
        'price' => __('pkg_starter_price'),
        'desc' => __('pkg_starter_desc'),
        'popular' => false,
        'features' => ['pkg_starter_f1', 'pkg_starter_f2', 'pkg_starter_f3', 'pkg_starter_f4', 'pkg_starter_f5'],
    ],
    [
        'name' => __('pkg_business'),
        'price' => __('pkg_business_price'),
        'desc' => __('pkg_business_desc'),
        'popular' => true,
        'features' => ['pkg_business_f1', 'pkg_business_f2', 'pkg_business_f3', 'pkg_business_f4', 'pkg_business_f5', 'pkg_business_f6'],
    ],
    [
        'name' => __('pkg_premium'),
        'price' => __('pkg_premium_price'),
        'desc' => __('pkg_premium_desc'),
        'popular' => false,
        'features' => ['pkg_premium_f1', 'pkg_premium_f2', 'pkg_premium_f3', 'pkg_premium_f4', 'pkg_premium_f5', 'pkg_premium_f6'],
    ],
];

$compare = [
    [__('compare_pages'), '5', '10', __('compare_custom')],
    [__('compare_responsive'), __('compare_yes'), __('compare_yes'), __('compare_yes')],
    [__('compare_cms'), __('compare_optional'), __('compare_yes'), __('compare_yes')],
    [__('compare_ecommerce'), __('compare_no'), __('compare_optional'), __('compare_yes')],
    [__('compare_seo'), __('compare_basic'), __('compare_advanced'), __('compare_advanced')],
    [__('compare_support'), __('compare_days_7'), __('compare_days_30'), __('compare_days_60')],
];
?>

<section class="page-hero page-hero--plain">
    <div class="max-width reveal">
        <h1><?= e(__('pricing_title')) ?></h1>
        <p><?= e(__('pricing_sub')) ?></p>
    </div>
</section>

<section class="section pricing-section">
    <div class="max-width">
        <div class="pricing-stack reveal">
            <?php foreach ($packages as $pkg): ?>
            <div class="price-card <?= $pkg['popular'] ? 'popular' : '' ?>">
                <?php if ($pkg['popular']): ?><span class="badge"><?= e(__('popular')) ?></span><?php endif; ?>
                <div class="price-card-top">
                    <div>
                        <h3><?= e($pkg['name']) ?></h3>
                        <p><?= e($pkg['desc']) ?></p>
                    </div>
                    <div class="price"><?= e($pkg['price']) ?></div>
                </div>
                <ul>
                    <?php foreach ($pkg['features'] as $fKey): ?>
                    <li><i class="fas fa-check"></i> <?= e(__($fKey)) ?></li>
                    <?php endforeach; ?>
                </ul>
                <a class="btn" href="<?= url('contact.php?plan=' . urlencode($pkg['name'])) ?>"><?= e(__('get_started')) ?></a>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="compare-wrap reveal">
            <h2><?= e(__('features_compare')) ?></h2>
            <div class="compare-scroll">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th><?= e(__('compare_feature')) ?></th>
                            <th><?= e(__('pkg_starter')) ?></th>
                            <th><?= e(__('pkg_business')) ?></th>
                            <th><?= e(__('pkg_premium')) ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compare as $row): ?>
                        <tr>
                            <?php foreach ($row as $i => $cell): ?>
                            <td<?= $i === 0 ? ' class="feature-name"' : '' ?>><?= e($cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cta-band pricing-cta reveal">
            <h2><?= e(__('contact_cta_title')) ?></h2>
            <p><?= e(__('contact_cta_sub')) ?></p>
            <a class="btn btn-primary" href="<?= url('contact.php') ?>"><?= e(__('cta_contact')) ?></a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
