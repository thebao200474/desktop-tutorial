<?php
use function htmlspecialchars as h;
?>
<div class="row gy-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
                    <div>
                        <h1 class="h4 mb-1">Bảng tuần hoàn hóa học</h1>
                        <p class="text-muted mb-0">Tra cứu nhanh ký hiệu, nguyên tử khối và mô tả từng nguyên tố.</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <input type="search" class="form-control" placeholder="Tìm theo tên hoặc ký hiệu" data-element-filter>
                    </div>
                </div>
                <div class="periodic-grid" data-element-grid>
                    <?php foreach ($elements as $element): ?>
                        <article class="periodic-card" data-element-name="<?= h(mb_strtolower($element['ten'] ?? '')); ?>" data-element-symbol="<?= h(mb_strtolower($element['kyhieu'] ?? '')); ?>">
                            <header class="periodic-card__header">
                                <span class="periodic-card__symbol"><?= h($element['kyhieu']); ?></span>
                                <span class="periodic-card__number">Z = <?= (int)$element['ma_nguyento']; ?></span>
                            </header>
                            <div class="periodic-card__body">
                                <h2 class="periodic-card__name h6 mb-1"><?= h($element['ten']); ?></h2>
                                <p class="mb-1 small text-muted">Nguyên tử khối: <?= h(number_format((float)$element['nguyentukhoi'], 2)); ?></p>
                                <p class="mb-1 small">Nhóm <?= h($element['nhom']); ?> · Chu kỳ <?= h($element['chuky']); ?></p>
                                <?php if (!empty($element['mota'])): ?>
                                    <p class="periodic-card__description small mb-0"><?= h(mb_strimwidth((string)$element['mota'], 0, 120, '…')); ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                    <?php if (empty($elements)): ?>
                        <p class="text-muted">Chưa có dữ liệu nguyên tố. Hãy thêm dữ liệu vào bảng <code>nguyento</code>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
