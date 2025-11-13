<?php
use function htmlspecialchars as h;
?>
<section class="py-4 py-md-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">🔬 Phương trình Hóa học Phổ Biến</h1>
        <p class="text-muted lead">Khám phá các phản ứng tiêu biểu và tra cứu nhanh chóng theo chất hoặc ký hiệu.</p>
        <form class="d-flex justify-content-center mt-4" method="get" action="<?= app_url('phuongtrinh'); ?>">
            <div class="input-group input-group-lg w-100 equation-search-wrapper" style="max-width: 540px;">
                <span class="input-group-text bg-success text-white">🔍</span>
                <input
                    type="search"
                    name="q"
                    class="form-control"
                    placeholder="Nhập chất hoặc ký hiệu (ví dụ: H₂, O₂, CO₂)"
                    value="<?= h($keyword ?? ''); ?>"
                    aria-label="Tìm kiếm phương trình"
                    data-equation-search
                >
                <button class="btn btn-success" type="submit">Tìm kiếm</button>
            </div>
        </form>
        <div class="position-relative d-flex justify-content-center">
            <div class="list-group equation-suggestions d-none shadow" data-equation-suggestions></div>
        </div>
    </div>

    <?php if (!empty($equations)): ?>
        <div class="row g-4">
            <?php foreach ($equations as $equation): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-success border-2 shadow-sm h-100 bg-light-subtle">
                        <div class="card-body d-flex flex-column">
                            <h2 class="card-title h4 fw-bold text-success"><?= h($equation['phuong_trinh'] ?? ''); ?></h2>
                            <div class="mt-3">
                                <span class="badge text-bg-primary me-2">Loại: <?= h($equation['loai_phan_ung'] ?? ''); ?></span>
                                <span class="badge text-bg-success">Nhóm: <?= h($equation['nhom_phan_ung'] ?? ''); ?></span>
                            </div>
                            <p class="card-text mt-3 flex-grow-1"><?= h($equation['giai_thich'] ?? ''); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center shadow-sm" role="alert">
            Không tìm thấy phương trình phù hợp.
        </div>
    <?php endif; ?>
</section>
<?php
$suggestionData = json_encode($allEquations ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$currentKeyword = json_encode($keyword ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
<script>
    window.ChemLearnEquationData = <?= $suggestionData ?: '[]'; ?>;
    window.ChemLearnEquationKeyword = <?= $currentKeyword ?: "''"; ?>;
</script>
<script src="<?= asset_url('js/phuongtrinh.js'); ?>" defer></script>
