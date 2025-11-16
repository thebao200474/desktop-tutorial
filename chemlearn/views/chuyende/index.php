<?php
use function htmlspecialchars as h;

$lessons = $lessons ?? [];
$coreTopics = $coreTopics ?? [];
$laws = $laws ?? [];
$formulas = $formulas ?? [];
$decorImages = $decorImages ?? [];
?>

<div class="module-hero card border-0 shadow-sm mb-5">
    <div class="card-body d-lg-flex align-items-center gap-4">
        <div class="flex-grow-1">
            <p class="text-uppercase text-primary fw-semibold mb-2">ChemLearn Modules</p>
            <h1 class="display-6 fs-2 fw-bold mb-3">🔬 Phân hệ Chuyên đề Hóa học</h1>
            <p class="text-muted mb-0">
                10 chuyên đề cốt lõi + mục Định luật + mục Công thức được trình bày theo dạng mục lục số hóa
                (1, 1.1, …) giúp dễ dàng đưa vào AI offline và bài giảng ChemLearn. Tất cả nội dung bám sát chương
                trình phổ thông – đại cương và đi kèm ví dụ minh họa.
            </p>
        </div>
        <div class="module-hero__illustration text-center mt-4 mt-lg-0">
            <img src="<?= asset_url('images/topics/core-atom.svg'); ?>" alt="Atom" class="img-fluid" width="180" height="180">
        </div>
    </div>
</div>

<?php if (!empty($decorImages)): ?>
    <div class="decor-gallery card border-0 shadow-sm mb-5">
        <div class="card-body">
            <div class="decor-grid">
                <?php foreach ($decorImages as $decor): ?>
                    <figure class="decor-item">
                        <img src="<?= asset_url('images/topics/' . h($decor['file'])); ?>" alt="<?= h($decor['alt']); ?>" width="120" height="120">
                        <figcaption class="small text-muted"><?= h($decor['alt']); ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">10 Chuyên đề Hóa học cốt lõi</h2>
            <p class="text-muted mb-0">Bám sát chương trình phổ thông – đại cương, đầy đủ ví dụ minh họa.</p>
        </div>
        <a href="<?= app_url(); ?>" class="btn btn-outline-primary">← Về trang chủ</a>
    </div>

    <div class="topic-grid">
        <?php foreach ($coreTopics as $topic): ?>
            <article class="topic-card card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="topic-icon rounded-circle flex-shrink-0 text-center">
                            <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold"><?= h($topic['code']); ?></span>
                            <img src="<?= asset_url('images/topics/' . h($topic['icon'])); ?>" alt="<?= h($topic['title']); ?>" width="56" height="56">
                        </div>
                        <div>
                            <h3 class="h6 mb-1"><?= h($topic['title']); ?></h3>
                            <p class="text-muted small mb-0">Module nền tảng</p>
                        </div>
                    </div>
                    <ul class="list-unstyled small mb-0">
                        <?php foreach ($topic['bullets'] as $point): ?>
                            <li class="d-flex gap-2 mb-1">
                                <span class="text-success">✔</span>
                                <span><?= h($point); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 module-panel">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= asset_url('images/topics/laws.svg'); ?>" width="64" height="64" alt="Định luật">
                        <div>
                            <h2 class="h5 mb-1">Mục Định luật hóa học</h2>
                            <p class="text-muted small mb-0">10 định luật kinh điển dùng trong mọi chuyên đề.</p>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush module-list">
                        <?php foreach ($laws as $law): ?>
                            <li class="list-group-item px-0 d-flex gap-3">
                                <span class="text-primary fw-bold">•</span>
                                <div>
                                    <p class="fw-semibold mb-1"><?= h($law['name']); ?></p>
                                    <p class="text-muted small mb-0"><?= h($law['desc']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 module-panel">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= asset_url('images/topics/formula.svg'); ?>" width="64" height="64" alt="Công thức">
                        <div>
                            <h2 class="h5 mb-1">Mục Công thức hóa học</h2>
                            <p class="text-muted small mb-0">Tổng hợp công thức tính nhanh – dùng được trên mọi đề.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($formulas as $formula): ?>
                            <div class="col-12">
                                <div class="formula-card p-3 rounded-4 border border-success-subtle">
                                    <p class="fw-semibold mb-2 text-success-emphasis"><?= h($formula['title']); ?></p>
                                    <ul class="list-unstyled small mb-0">
                                        <?php foreach ($formula['lines'] as $line): ?>
                                            <li>• <?= h($line); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Kho bài giảng chi tiết (<?= count($lessons); ?> chuyên đề)</h2>
        <span class="text-muted small">Nguồn: bảng <code>baigiang</code></span>
    </div>

    <?php if (empty($lessons)): ?>
        <div class="alert alert-warning shadow-sm">Chưa có chuyên đề trong cơ sở dữ liệu. Hãy thêm dữ liệu vào bảng <code>baigiang</code>.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($lessons as $lesson): ?>
                <div class="col-md-6">
                    <div class="card border-success-subtle border-start border-4 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <img src="<?= asset_url('images/topics/reaction.svg'); ?>" width="32" height="32" alt="Lesson icon">
                                <h5 class="card-title mb-0">
                                    <a href="<?= app_url('chuyende_chitiet.php?id=' . (int)$lesson['ma_baigiang']); ?>" class="text-decoration-none">
                                        <?= h($lesson['ten_baigiang']); ?>
                                    </a>
                                </h5>
                            </div>
                            <p class="card-text text-muted small mb-3"><?= h(mb_strimwidth(strip_tags($lesson['noidung'] ?? ''), 0, 200, '...')); ?></p>
                            <a href="<?= app_url('chuyende_chitiet.php?id=' . (int)$lesson['ma_baigiang']); ?>" class="btn btn-success btn-sm">Đọc chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
