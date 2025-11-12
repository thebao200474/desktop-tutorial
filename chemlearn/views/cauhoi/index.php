<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 mb-1">Bài luyện tập trắc nghiệm</h1>
                <p class="text-muted mb-0">Chọn đáp án đúng cho mỗi câu. Kết quả sẽ được lưu vào tiến độ khi bạn đăng nhập.</p>
            </div>
            <a href="<?= app_url(); ?>" class="btn btn-outline-secondary">← Trang chủ</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-warning"><?= h($message); ?></div>
        <?php endif; ?>

        <form method="post" class="card border-0 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
            <div class="card-body p-4">
                <?php if (empty($questions)): ?>
                    <p class="text-muted">Hiện chưa có câu hỏi. Vui lòng thêm dữ liệu vào bảng <code>cauhoi</code>.</p>
                <?php else: ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <?php $questionId = (int)$question['ma_cauhoi']; ?>
                        <fieldset class="mb-4">
                            <legend class="fw-semibold">Câu <?= $index + 1; ?>: <?= h($question['noidung']); ?></legend>
                            <?php
                            $options = [
                                'A' => $question['dapan_a'],
                                'B' => $question['dapan_b'],
                                'C' => $question['dapan_c'],
                                'D' => $question['dapan_d'],
                            ];
                            ?>
                            <?php foreach ($options as $key => $option): ?>
                                <?php $inputId = 'q' . $questionId . $key; ?>
                                <?php $selected = $results[$questionId]['userAnswer'] ?? ''; ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?= $questionId; ?>]" id="<?= h($inputId); ?>" value="<?= h($key); ?>" <?= $selected === $key ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?= h($inputId); ?>">
                                        <strong><?= h($key); ?>.</strong> <?= h($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <?php if (isset($results[$questionId])): ?>
                                <?php $isCorrect = $results[$questionId]['isCorrect']; ?>
                                <div class="mt-2">
                                    <?php if ($isCorrect): ?>
                                        <span class="badge text-bg-success">Chính xác!</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-danger">Chưa đúng</span>
                                        <small class="text-muted ms-2">Đáp án đúng: <?= h($results[$questionId]['correctAnswer']); ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </fieldset>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white text-end">
                <button type="submit" class="btn btn-primary">Nộp bài</button>
            </div>
        </form>

        <?php if ($score !== null): ?>
            <div class="alert alert-success mt-4">
                🎉 Bạn đạt <?= h($score); ?> điểm. Tiếp tục luyện tập để cải thiện kiến thức nhé!
            </div>
        <?php endif; ?>
    </div>
</div>
