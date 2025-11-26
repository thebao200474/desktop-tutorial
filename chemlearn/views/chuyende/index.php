<?php
use function htmlspecialchars as h;

$lessons = $lessons ?? [];
$topicGrid = $topicGrid ?? [];
$topicDetails = $topicDetails ?? [];
$laws = $laws ?? [];
$formulas = $formulas ?? [];
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

<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Danh mục 8 chuyên đề</h2>
            <p class="text-muted mb-0">Bấm vào từng chuyên đề để mở nội dung chi tiết và làm trắc nghiệm.</p>
        </div>
        <a href="<?= app_url(); ?>" class="btn btn-outline-primary">← Về trang chủ</a>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-3">Danh sách chuyên đề hiển thị dọc để bạn dễ theo dõi. Nhấn để xem nội dung.</p>
                    <div class="topic-nav-vertical" role="list">
                        <?php foreach ($topicGrid as $topic): ?>
                            <button type="button"
                                    class="topic-nav-btn btn btn-light d-flex justify-content-between align-items-center"
                                    data-topic-code="<?= h($topic['code']); ?>"
                                    aria-controls="topic-detail-<?= h($topic['code']); ?>"
                                    role="listitem">
                                <div class="text-start">
                                    <span class="d-block text-uppercase text-muted small">Chuyên đề <?= h($topic['code']); ?></span>
                                    <span class="fw-semibold text-dark"><?= h($topic['title']); ?></span>
                                </div>
                                <span class="text-primary">→</span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div id="topic-detail-placeholder" class="alert alert-info shadow-sm rounded-4">Chọn một chuyên đề ở danh sách bên trái để xem nội dung, ví dụ và bộ trắc nghiệm.</div>
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

                            <div class="row g-4 align-items-stretch">
                                <div class="col-md-7">
                                    <div class="topic-detail__box h-100">
                                        <h4 class="h6 text-primary">📘 Nội dung chính</h4>
                                        <ul class="list-unstyled small mb-0 topic-detail__list">
                                            <?php foreach ($topic['content'] as $line): ?>
                                                <li>✔ <?= h($line); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="topic-detail__box example-box h-100">
                                        <h4 class="h6 text-warning">🧪 Ví dụ</h4>
                                        <p class="small text-muted mb-0"><?= h($topic['example']); ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="topic-quiz-block mt-4">
                                <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
                                    <h4 class="h6 text-success mb-0">📝 Trắc nghiệm (5 câu)</h4>
                                    <span class="badge bg-primary-subtle text-primary">Cộng điểm rank khi nộp</span>
                                </div>
                                <form class="topic-quiz-form" data-topic-code="<?= h($topic['code']); ?>" data-endpoint="<?= app_url('chuyende_quiz.php'); ?>">
                                    <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
                                    <?php foreach ($topic['quiz'] as $index => $quiz): ?>
                                        <?php $questionIndex = (string) $index; ?>
                                        <fieldset class="quiz-question" data-question-index="<?= h($questionIndex); ?>">
                                            <legend class="fw-semibold small mb-2"><?= h($quiz['question']); ?></legend>
                                            <div class="quiz-answer-list">
                                                <?php foreach ($quiz['options'] as $optionIndex => $optionText): ?>
                                                    <?php
                                                    $optionCode = null;
                                                    if (preg_match('/^([A-D])\./u', $optionText, $matches)) {
                                                        $optionCode = strtoupper($matches[1]);
                                                    } else {
                                                        $optionCode = chr(65 + $optionIndex);
                                                    }
                                                    $inputId = sprintf('quiz-%s-%s-%s', $topic['code'], $questionIndex, $optionCode);
                                                    ?>
                                                    <div class="form-check quiz-answer">
                                                        <input class="form-check-input" type="radio" name="answers[<?= h($questionIndex); ?>]" id="<?= h($inputId); ?>" value="<?= h($optionCode); ?>">
                                                        <label class="form-check-label" for="<?= h($inputId); ?>">
                                                            <strong><?= h($optionCode); ?>.</strong> <?= h($optionText); ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </fieldset>
                                    <?php endforeach; ?>
                                    <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mt-3">
                                        <button class="btn btn-primary btn-sm" type="submit">Nộp bài & cộng rank</button>
                                        <small class="text-muted">Điểm rank = số câu đúng (cần đăng nhập).</small>
                                    </div>
                                    <div class="alert alert-secondary mt-3 d-none" role="status" data-quiz-result></div>
                                </form>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
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
    const topicButtons = document.querySelectorAll('.topic-nav-btn');
    const detailBlocks = document.querySelectorAll('[data-topic-detail]');
    const placeholder = document.getElementById('topic-detail-placeholder');

    const clearQuizState = (form) => {
        form.querySelectorAll('.quiz-answer').forEach((answer) => {
            answer.classList.remove('is-correct', 'is-incorrect');
        });
    };

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
            } else {
                placeholder?.classList.remove('d-none');
            }
        });
    });

    const quizForms = document.querySelectorAll('.topic-quiz-form');
    quizForms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const endpoint = form.getAttribute('data-endpoint');
            const topicCode = form.getAttribute('data-topic-code');
            const csrfInput = form.querySelector('input[name="csrf_token"]');
            const csrf = csrfInput ? csrfInput.value : '';
            const resultBox = form.querySelector('[data-quiz-result]');
            const submitButton = form.querySelector('button[type="submit"]');
            const fieldsets = form.querySelectorAll('[data-question-index]');

            if (!endpoint || !topicCode) {
                return;
            }

            clearQuizState(form);

            const payload = new FormData();
            payload.append('topic', topicCode);
            payload.append('csrf_token', csrf);

            fieldsets.forEach((fieldset) => {
                const index = fieldset.getAttribute('data-question-index');
                const checked = fieldset.querySelector('input[type="radio"]:checked');
                if (index && checked) {
                    payload.append(`answers[${index}]`, checked.value);
                }
            });

            if (submitButton) {
                submitButton.disabled = true;
            }
            if (resultBox) {
                resultBox.classList.add('d-none');
                resultBox.classList.remove('alert-success', 'alert-danger');
                resultBox.classList.add('alert-secondary');
                resultBox.textContent = 'Đang chấm...';
            }

            try {
                const response = await fetch(endpoint, { method: 'POST', body: payload, credentials: 'same-origin' });
                const data = await response.json();

                if (!data || !data.ok) {
                    throw new Error((data && data.message) || 'Không thể chấm điểm.');
                }

                const detailMap = new Map();
                (data.details || []).forEach((detail) => {
                    detailMap.set(String(detail.index), detail);
                });

                fieldsets.forEach((fieldset) => {
                    const idx = fieldset.getAttribute('data-question-index');
                    const detail = detailMap.get(idx);
                    fieldset.querySelectorAll('.quiz-answer').forEach((answer) => {
                        const input = answer.querySelector('input[type="radio"]');
                        if (!detail || !input) {
                            return;
                        }
                        if (input.value === detail.correctAnswer) {
                            answer.classList.add('is-correct');
                        } else if (detail.userAnswer && input.value === detail.userAnswer) {
                            answer.classList.add('is-incorrect');
                        }
                    });
                });

                if (resultBox) {
                    resultBox.textContent = data.message || `Kết quả: ${data.scoreLabel || ''}`;
                    resultBox.classList.remove('alert-secondary', 'alert-danger');
                    resultBox.classList.add('alert-success');
                    resultBox.classList.remove('d-none');
                }
            } catch (error) {
                if (resultBox) {
                    const message = error instanceof Error ? error.message : 'Đã xảy ra lỗi.';
                    resultBox.textContent = message;
                    resultBox.classList.remove('alert-secondary', 'alert-success');
                    resultBox.classList.add('alert-danger');
                    resultBox.classList.remove('d-none');
                }
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                }
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
