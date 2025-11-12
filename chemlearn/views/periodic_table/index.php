<?php
use function htmlspecialchars as h;

$categories = [
    'kiem' => 'Kim loại kiềm',
    'kiem_tho' => 'Kim loại kiềm thổ',
    'chuyen_tiep' => 'Kim loại chuyển tiếp',
    'hau_chuyen_tiep' => 'Kim loại hậu chuyển tiếp',
    'a_kim' => 'Á kim',
    'phi_kim' => 'Phi kim',
    'halogen' => 'Halogen',
    'khi_hiem' => 'Khí hiếm',
    'lantanide' => 'Lantanide (La–Lu)',
    'actinide' => 'Actinide (Ac–Lr)',
    'chua_xd' => 'Đặc tính chưa xác định rõ'
];
?>
<div class="periodic-wrapper">
    <header class="periodic-title">
        <h1 class="display-6 fw-semibold">Bảng tuần hoàn hóa học (118 nguyên tố)</h1>
        <p class="text-muted mb-0">Sắp xếp theo nhóm (1–18) và chu kỳ (1–7) tương tự bảng chuẩn quốc tế.</p>
        <div class="periodic-toolbar">
            <input type="search" id="periodic-search" placeholder="Tìm ký hiệu hoặc tên nguyên tố..." class="form-control">
            <select id="periodic-filter" class="form-select">
                <option value="">Tất cả nhóm</option>
                <?php foreach ($categories as $key => $label): ?>
                    <?php if (in_array($key, ['lantanide', 'actinide', 'chua_xd'], true)): continue; endif; ?>
                    <option value="<?= h($key); ?>"><?= h($label); ?></option>
                <?php endforeach; ?>
                <option value="lantanide">Lantanide</option>
                <option value="actinide">Actinide</option>
                <option value="chua_xd">Đặc tính chưa xác định</option>
            </select>
        </div>
    </header>

    <div class="ptable-grid" data-periodic-grid>
        <?php foreach ($elementsVar as $element): ?>
            <?php if ($element['group'] !== null): ?>
                <?php
                $category = $element['category'] ?? 'chua_xd';
                $symbol = $element['symbol'] ?? '';
                $nameVi = $element['name_vi'] ?? '';
                $tooltip = ($element['Z'] ?? '') . ' – ' . ($element['name_en'] ?? '') . ' (' . $symbol . ')';
                ?>
                <div class="periodic-cell cat-<?= h($category); ?>"
                     style="grid-column: <?= (int) $element['group']; ?>; grid-row: <?= (int) $element['period']; ?>;"
                     data-symbol="<?= h($symbol); ?>"
                     data-name="<?= h($nameVi); ?>"
                     data-category="<?= h($category); ?>"
                     title="<?= h($tooltip); ?>">
                    <div class="z"><?= (int) $element['Z']; ?></div>
                    <div class="sym"><?= h($symbol); ?></div>
                    <div class="name"><?= h($nameVi); ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <h3 class="periodic-series-heading">Lantanide (La–Lu)</h3>
    <div class="series-grid">
        <?php foreach ($elementsVar as $element): ?>
            <?php if (($element['category'] ?? '') === 'lantanide'): ?>
                <?php
                $symbol = $element['symbol'] ?? '';
                $nameVi = $element['name_vi'] ?? '';
                $tooltip = ($element['Z'] ?? '') . ' – ' . ($element['name_en'] ?? '') . ' (' . $symbol . ')';
                ?>
                <div class="periodic-cell cat-lantanide"
                     data-symbol="<?= h($symbol); ?>"
                     data-name="<?= h($nameVi); ?>"
                     data-category="lantanide"
                     title="<?= h($tooltip); ?>">
                    <div class="z"><?= (int) $element['Z']; ?></div>
                    <div class="sym"><?= h($symbol); ?></div>
                    <div class="name"><?= h($nameVi); ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <h3 class="periodic-series-heading">Actinide (Ac–Lr)</h3>
    <div class="series-grid">
        <?php foreach ($elementsVar as $element): ?>
            <?php if (($element['category'] ?? '') === 'actinide'): ?>
                <?php
                $symbol = $element['symbol'] ?? '';
                $nameVi = $element['name_vi'] ?? '';
                $tooltip = ($element['Z'] ?? '') . ' – ' . ($element['name_en'] ?? '') . ' (' . $symbol . ')';
                ?>
                <div class="periodic-cell cat-actinide"
                     data-symbol="<?= h($symbol); ?>"
                     data-name="<?= h($nameVi); ?>"
                     data-category="actinide"
                     title="<?= h($tooltip); ?>">
                    <div class="z"><?= (int) $element['Z']; ?></div>
                    <div class="sym"><?= h($symbol); ?></div>
                    <div class="name"><?= h($nameVi); ?></div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <ul class="periodic-legend">
        <li><span class="dot cat-kiem"></span> Kim loại kiềm</li>
        <li><span class="dot cat-kiem_tho"></span> Kim loại kiềm thổ</li>
        <li><span class="dot cat-chuyen_tiep"></span> Kim loại chuyển tiếp</li>
        <li><span class="dot cat-hau_chuyen_tiep"></span> Hậu chuyển tiếp</li>
        <li><span class="dot cat-a_kim"></span> Á kim</li>
        <li><span class="dot cat-phi_kim"></span> Phi kim</li>
        <li><span class="dot cat-halogen"></span> Halogen</li>
        <li><span class="dot cat-khi_hiem"></span> Khí hiếm</li>
        <li><span class="dot cat-lantanide"></span> Lantanide</li>
        <li><span class="dot cat-actinide"></span> Actinide</li>
        <li><span class="dot cat-chua_xd"></span> Đặc tính chưa xác định</li>
    </ul>
</div>
<script src="<?= asset_url('js/periodic-table.js'); ?>"></script>
