<?php
use function htmlspecialchars as h;

$lessons = $lessons ?? [];
$topicGrid = $topicGrid ?? [];
$topicDetails = $topicDetails ?? [];
$laws = $laws ?? [];
$formulas = $formulas ?? [];
$decorImages = $decorImages ?? [];
?>

<div class="module-hero card border-0 shadow-sm mb-5">
    <div class="card-body d-lg-flex align-items-center gap-4">
        <div class="flex-grow-1">
            <p class="text-uppercase text-primary fw-semibold mb-2">ChemLearn Modules</p>
            <h1 class="display-6 fs-2 fw-bold mb-3">🔬 8 chuyên đề Hóa học cốt lõi</h1>
            <p class="text-muted mb-0">
                Bộ nội dung rút gọn gồm 8 chuyên đề trọng tâm, mỗi chuyên đề có phần lý thuyết, ví dụ và 5 câu trắc nghiệm
                kèm đáp án để dùng cho AI offline và giáo án ChemLearn.
            </p>
        </div>
        <div class="module-hero__illustration text-center mt-4 mt-lg-0">
            <img src="<?= asset_url('images/topics/core-atom.svg'); ?>" alt="Atom" class="img-fluid" width="180" height="180">
        </div>
    </div>
</div>

<?php if (!empty($decorImages)): ?>
    <div class="decor-ribbon card border-0 shadow-sm mb-5">
        <div class="card-body">
            <div class="decor-ribbon__track" role="list">
                <?php foreach ($decorImages as $decor): ?>
                    <div class="decor-chip" role="listitem">
                        <img src="<?= asset_url('images/topics/' . h($decor['file'])); ?>" alt="<?= h($decor['alt']); ?>" width="56" height="56">
                        <span class="fw-semibold small text-secondary"><?= h($decor['alt']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Danh mục 8 chuyên đề (grid 3×)</h2>
            <p class="text-muted mb-0">Chỉ hiển thị mã và tên chuyên đề để dễ quan sát tổng quan.</p>
        </div>
        <a href="<?= app_url(); ?>" class="btn btn-outline-primary">← Về trang chủ</a>
    </div>

    <div class="topic-grid-simple" role="list">
        <?php foreach ($topicGrid as $topic): ?>
            <button type="button"
                    class="topic-pill card border-0 shadow-sm text-center"
                    data-topic-code="<?= h($topic['code']); ?>"
                    aria-controls="topic-detail-<?= h($topic['code']); ?>"
                    role="listitem">
                <div class="card-body py-4">
                    <span class="badge rounded-pill bg-primary-subtle text-primary fw-semibold mb-2">Chuyên đề <?= h($topic['code']); ?></span>
                    <p class="fw-semibold mb-0"><?= h($topic['title']); ?></p>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
</section>

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Nội dung chi tiết + ví dụ + trắc nghiệm</h2>
            <p class="text-muted mb-0">Mỗi chuyên đề gồm phần lý thuyết, ví dụ minh họa và 5 câu hỏi trắc nghiệm.</p>
        </div>
    </div>

    <div id="topic-detail-placeholder" class="alert alert-info shadow-sm rounded-4">Chọn một chuyên đề ở lưới phía trên để xem nội dung, ví dụ và bộ trắc nghiệm.</div>

    <div class="topic-detail-stack">
        <?php foreach ($topicDetails as $topic): ?>
            <article class="topic-detail card border-0 shadow-sm mb-4 d-none" id="topic-detail-<?= h($topic['code']); ?>" data-topic-detail="<?= h($topic['code']); ?>">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                        <div>
                            <p class="text-uppercase small text-muted mb-1">Chuyên đề <?= h($topic['code']); ?></p>
                            <h3 class="h5 mb-1"><?= h($topic['title']); ?></h3>
                            <p class="text-muted small mb-0"><?= h($topic['summary']); ?></p>
                        </div>
                        <span class="badge bg-success-subtle text-success fw-semibold">🧪 Có ví dụ & trắc nghiệm</span>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-4">
                            <h4 class="h6 text-primary">📘 Nội dung chính</h4>
                            <ul class="list-unstyled small mb-0 topic-detail__list">
                                <?php foreach ($topic['content'] as $line): ?>
                                    <li>✔ <?= h($line); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-lg-4">
                            <h4 class="h6 text-warning">🧪 Ví dụ</h4>
                            <p class="small text-muted mb-0"><?= h($topic['example']); ?></p>
                        </div>
                        <div class="col-lg-4">
                            <h4 class="h6 text-success">📝 Trắc nghiệm (5 câu)</h4>
                            <ol class="quiz-list small mb-0">
                                <?php foreach ($topic['quiz'] as $index => $quiz): ?>
                                    <li>
                                        <p class="mb-1 fw-semibold"><?= h($quiz['question']); ?></p>
                                        <ul class="list-unstyled mb-1">
                                            <?php foreach ($quiz['options'] as $option): ?>
                                                <li><?= h($option); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <span class="badge bg-primary-subtle text-primary">Đáp án: <?= h($quiz['answer']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                    </div>
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const topicButtons = document.querySelectorAll('[data-topic-code]');
    const detailBlocks = document.querySelectorAll('[data-topic-detail]');
    const placeholder = document.getElementById('topic-detail-placeholder');

    topicButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const targetCode = btn.getAttribute('data-topic-code');
            const targetBlock = document.getElementById(`topic-detail-${targetCode}`);

            detailBlocks.forEach((block) => block.classList.add('d-none'));
            topicButtons.forEach((button) => button.classList.remove('active'));

            if (targetBlock) {
                targetBlock.classList.remove('d-none');
                btn.classList.add('active');
                placeholder?.classList.add('d-none');
                targetBlock.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
            }
        });
    });
});
</script>

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
