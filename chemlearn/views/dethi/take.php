<?php
use function htmlspecialchars as h;
?>
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h4 mb-1"><?= h($exam['ten_de'] ?? 'Đề thi'); ?></h1>
                <p class="text-muted mb-0">Mã đề <?= h($exam['ma_de'] ?? 'N/A'); ?> · Năm <?= h($exam['nam'] ?? ''); ?></p>
            </div>
            <a href="<?= app_url('de_thi.php'); ?>" class="btn btn-outline-secondary">← Chọn đề khác</a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-warning"><?= h($message); ?></div>
        <?php endif; ?>

        <form method="post" class="card border-0 shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= h($csrfToken); ?>">
            <div class="card-body p-4">
                <?php if (empty($questions)): ?>
                    <p class="text-muted">Đề thi chưa có câu hỏi. Vui lòng quay lại sau.</p>
                <?php else: ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <?php $questionId = (int)$question['id']; ?>
                        <fieldset class="mb-4">
                            <legend class="fw-semibold">Câu <?= $index + 1; ?>: <?= h($question['noi_dung']); ?></legend>
                            <?php
                            $options = [
                                'A' => $question['dapan_a'],
                                'B' => $question['dapan_b'],
                                'C' => $question['dapan_c'],
                                'D' => $question['dapan_d'],
                            ];
                            ?>
                            <?php foreach ($options as $key => $option): ?>
                                <?php $inputId = 'e' . $questionId . $key; ?>
                                <?php $selected = $results[$questionId]['userAnswer'] ?? ''; ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[<?= $questionId; ?>]" id="<?= h($inputId); ?>" value="<?= h($key); ?>" <?= $selected === $key ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="<?= h($inputId); ?>">
                                        <strong><?= h($key); ?>.</strong> <?= h($option); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <?php if ($showAnswers && isset($results[$questionId])): ?>
                                <?php $result = $results[$questionId]; ?>
                                <div class="mt-2">
                                    <?php if ($result['isCorrect']): ?>
                                        <span class="badge text-bg-success">Đúng</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-danger">Sai</span>
                                        <small class="text-muted ms-2">Đáp án đúng: <?= h($result['correctAnswer']); ?></small>
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
            <div class="alert alert-info mt-4">
                Kết quả: <?= h($score); ?>. Đáp án đã được hiển thị cho từng câu hỏi.
            </div>
        <?php endif; ?>
    </div>
</div>
